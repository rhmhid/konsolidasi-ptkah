<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Adodb
{
	public static $adodb;

	function __construct () /*{{{*/
	{
		global $ADODB_FETCH_MODE;

		$ADODB_FETCH_MODE = env('DB_FETCHMODE');

		require_once APPPATH . '../vendor/adodb/adodb-php/adodb.inc.php';

		self::$adodb = ADONewConnection(env('DB_DRIVER'));

		if (!self::$adodb->Connect('host='.env('DB_HOSTNAME').' port='.env('DB_PORT').' dbname='.env('DB_NAME').' user='.env('DB_USERNAME').' password='.env('DB_PASSWORD')))
		{
			show_error('Connection Error');
			log_message('debug', "Connection Error");
		}

		self::$adodb->debug = env('DB_DEBUG');

		// SET DATESTYLE
		self::$adodb->Execute("SET DATESTYLE = 'iso, dmy'");

		log_message('debug', "ADODB Class Initialized");
	} /*}}}*/

		public function init($group = 'default') 
		{

		    $prefix = ($group === 'default') ? 'DB_' : strtoupper($group) . '_';
		    $hostname = env($prefix . 'HOSTNAME');
		    $username = env($prefix . 'USERNAME');
		    $password = env($prefix . 'PASSWORD');
		    $database = env($prefix . 'NAME');
		    $driver   = env($prefix . 'DRIVER');
		    $port     = env($prefix . 'PORT');

		    // Validasi apakah konfigurasi ada
		    if (!$hostname || !$database) {
		        die("Error: Konfigurasi untuk group [$group] tidak ditemukan di .env (Prefix: $prefix)");
		    }

		    $db = ADONewConnection($driver);
		    
		    // Gunakan port jika tersedia di .env
		    $host_conn = $port ? $hostname . ":" . $port : $hostname;
		    
		    if (!$db->Connect($host_conn, $username, $password, $database)) {
		        die("Gagal Terhubung ke Group [$group]: " . $db->ErrorMsg());
		    }

		    return $db; 
		}

	public function initx ($group = 'default') /*{{{*/
	{
		return self::$adodb;
	} /*}}}*/
}
?>
