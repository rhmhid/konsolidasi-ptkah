<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Bridging
{
    private static $clients = [];
    private static $configs = [];

    private static function _getConfig ($branch_code)
    {
        $branch_code = strtoupper(trim($branch_code));

        if (isset(self::$configs[$branch_code]))
            return self::$configs[$branch_code];

        $sql = "SELECT cabang_url, cabang_user, cabang_pass 
                FROM branch 
                WHERE branch_code = ?";
        $rs = DB::Execute($sql, [$branch_code]);

        if (!$rs->EOF)
        {
            self::$configs[$branch_code] = [
                'base_uri'  => $rs->fields['cabang_url'].'/rest.php/api/v1/',
                'username'  => $rs->fields['cabang_user'],
                'password'  => $rs->fields['cabang_pass'],
                'url_login' => 'login'
            ];

            return self::$configs[$branch_code];
        }

        return false;
    }

    /**
     * Singleton Dinamis: Inisialisasi Guzzle Client
     */
    private static function getClient ($branch_code)
    {
        $branch_code = strtoupper(trim($branch_code));

        if (!isset(self::$clients[$branch_code]))
        {
            $config = self::_getConfig($branch_code);

            if (!$config) {
                throw new Exception("Konfigurasi API untuk Kode Cabang [{$branch_code}] tidak ditemukan.");
            }

            self::$clients[$branch_code] = new Client([
                'base_uri' => $config['base_uri'], 
                'timeout'  => 10.0, // Sesuai request: maksimal 10 detik
                'verify'   => false 
            ]);
        }
        return self::$clients[$branch_code];
    }

    /**
     * Request GET 
     */
    public static function get($branch_code, $endpoint, $params = [], $headers = [], $is_retry = false)
    {
        $branch_code = strtoupper(trim($branch_code));
        
        // Panggil inject_token (sekarang otomatis generate kalau belum ada)
        $headers = self::_inject_token($branch_code, $headers);

        try {
            $response = self::getClient($branch_code)->request('GET', $endpoint, [
                'headers' => $headers,
                'query'   => $params
            ]);

            return self::_handleSuccess($response);

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            
            // Ambil raw body untuk ngecek pesan error dari API
            $rawBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            $is_token_invalid = isset($rawBody['error']['message']) && $rawBody['error']['message'] == 'Token Invalid.';

            // Auto-retry jika token kedaluwarsa (HTTP 400 + Token Invalid)
            if ($statusCode == 400 && $is_token_invalid && !$is_retry) {
                $newToken = self::_get_new_token($branch_code);
                
                if ($newToken) {
                    $headers['Authorization'] = 'Bearer ' . $newToken;
                    return self::get($branch_code, $endpoint, $params, $headers, true);
                }
            }

            return self::_handleError($e);
        } catch (Exception $e) {
            return self::_handleCustomError($e->getMessage());
        }
    }

    /**
     * Request POST 
     */
    public static function post($branch_code, $endpoint, $payload = [], $headers = [], $is_form_urlencoded = false, $is_retry = false)
    {
        $branch_code = strtoupper(trim($branch_code));
        
        // Panggil inject_token (sekarang otomatis generate kalau belum ada)
        $headers = self::_inject_token($branch_code, $headers);

        try {
            $options = ['headers' => $headers];
            
            if ($is_form_urlencoded) {
                $options['form_params'] = $payload;
            } else {
                $options['json'] = $payload;
            }

            $response = self::getClient($branch_code)->request('POST', $endpoint, $options);
            return self::_handleSuccess($response);

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            
            // Ambil raw body untuk ngecek pesan error dari API
            $rawBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            $is_token_invalid = isset($rawBody['error']['message']) && $rawBody['error']['message'] == 'Token Invalid.';

            // Auto-retry jika token kedaluwarsa (HTTP 400 + Token Invalid)
            if ($statusCode == 400 && $is_token_invalid && !$is_retry) {
                $newToken = self::_get_new_token($branch_code);
                
                if ($newToken) {
                    $headers['Authorization'] = 'Bearer ' . $newToken;
                    return self::post($branch_code, $endpoint, $payload, $headers, $is_form_urlencoded, true);
                }
            }

            return self::_handleError($e);
        } catch (Exception $e) {
            return self::_handleCustomError($e->getMessage());
        }
    }

    /**
     * PRIVATE: Generate token baru
     */
    private static function _get_new_token($branch_code)
    {
        try {
            $config = self::_getConfig($branch_code);

            $payload = [
                'username'  => $config['username'],
                'password'  => $config['password']
            ];

            $response = self::getClient($branch_code)->request('POST', $config['url_login'], [
                'form_params' => $payload
            ]);

            // Di Guzzle getContents() cuman bisa dipanggil sekali, lebih aman di-cast ke string
            $body = json_decode((string) $response->getBody(), true);
            
            if (isset($body['token'])) {
                $token = $body['token'];

                Auth::session('branch_token_' . $branch_code, $token);
                
                return $token;
            }

            return false;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * PRIVATE: Suntik token milik cabang tersebut dari session
     */
    private static function _inject_token($branch_code, $headers)
    {
        $token = Auth::session('branch_token_' . $branch_code);
        
        // JIKA TOKEN KOSONG (Belum pernah di-create), AUTO CREATE SEKARANG
        if (!$token) {
            $token = self::_get_new_token($branch_code);
        }
        
        if ($token && !isset($headers['Authorization'])) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        
        return $headers;
    }

    private static function _handleSuccess($response)
    {
        /*return [
            'status' => true,
            'code'   => $response->getStatusCode(),
            'data'   => json_decode((string) $response->getBody(), true)
        ];*/

        return json_decode((string) $response->getBody(), true);
    }

    private static function _handleError(RequestException $e)
    {
        /*$statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
        $errorMessage = $e->getMessage();
        $errorBody = [];

        if ($e->hasResponse()) {
            $errorBody = json_decode((string) $e->getResponse()->getBody(), true);
        }

        return [
            'status'  => false,
            'code'    => $statusCode,
            'message' => $errorMessage,
            'data'    => $errorBody
        ];*/

        return json_decode((string) $e->getResponse()->getBody(), true);
    }

    private static function _handleCustomError($message)
    {
        /*return [
            'status'  => false,
            'code'    => 500,
            'message' => $message,
            'data'    => []
        ];*/

        return json_decode((string) $message, true);
    }
}