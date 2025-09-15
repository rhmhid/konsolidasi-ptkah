<?php if(! defined('BASEPATH')) exit('No direct script access allowed');

use PHPJasper\PHPJasper;
use Luthier\Auth;

class Myjasper
{
 	private static $dir = VIEWPATH.'jasper_files/';
 	private static $output = VIEWPATH.'jasper_files/temp';
 	private static $jasper;

 	function __construct () /*{{{*/
 	{
 		self::$jasper = new PHPJasper;
 	} /*}}}*/

 	static function compile_file ($nama_file = '', $debug = false) /*{{{*/
 	{
 		$file = self::$dir.$nama_file.'.jrxml';

 		if (!file_exists($file))
 			die('File '.$nama_file.'.jrxml tidak ditemukan');

		if (!$debug)
			self::$jasper->compile($file)->execute();
		else
		{
			$x = self::$jasper->compile($file)->output();

			myprint_r($x);
			die;
		}
 	} /*}}}*/

 	static function pdf ($jasper_file = '') /*{{{*/
 	{
 		$file = str_replace('.tmp', '', $jasper_file).'.pdf';

		if (!file_exists($file))
		    die("File ".$file." PDF tidak ditemukan !");

		// Header content type
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="'.basename($file).'"');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');

		@flush();
		@readfile($file);
		@unlink($file);
 	} /*}}}*/

 	public static function print_pdf ($nama_file = '', $params = array(), $compile = false, $debug = false) /*{{{*/
 	{
 		if ($nama_file == '')
 			die("System Error: Nama file wajib disertakan !");

 		$input = self::$dir.$nama_file.'.jasper';

		if ($compile OR !file_exists($input))
		    self::compile_file($nama_file);

		$options = [
		    'format' 		=> ['pdf'],
		    'locale' 		=> 'en',
		    'params' 		=> $params,
		    /*'db_connection'	=>
		    [
		        'driver' 	=> env('DB_DRIVER3'),
		        'username' 	=> env('DB_USERNAME'),
		        'password' 	=> env('DB_PASSWORD'),
		        'host' 		=> env('DB_HOSTNAME'),
		        'database'	=> env('DB_NAME'),
		        'port' 		=> env('DB_PORT')
		    ]*/
		];

		if (!$debug)
		{
			$temp_jasper = tempnam(self::$output, 'jasper');
			copy($input, $temp_jasper);

			// register shutdown utk otomatis hapus tmp file, jadi kalau script berhenti tetap dihapus
			register_shutdown_function('unlink', $temp_jasper);

		    self::$jasper->process(
				$temp_jasper,
				self::$output,
				$options
		    )->execute();
		}
		elseif (Auth::user()->pid == 1)
		{
		    $x = self::$jasper->process(
				    $temp_jasper,
				    self::$output,
				    $options
			    )->output();

		    myprint_r($x);
		    die;
		}
		else
		    die("Under maintenance @SuperAdmin");

		self::pdf($temp_jasper);
 	} /*}}}*/

 	public static function print_pdf_json ($nama_file = '', $params = array(), $json_data = '', $data_query = '', $compile = false, $debug = false, $convet_to = 'pdf') /*{{{*/
 	{
 		if ($nama_file == '')
 			die("Error: @param:nama_file is empty !");

 		$input = self::$dir.$nama_file.'.jasper';

		if ($compile OR !file_exists($input))
		    self::compile_file($nama_file);

		if (!file_exists($input))
			self::compile_file($nama_file);

		$options = [
		    'format' => [$convet_to],
		    'params' => $params,
		    'locale' => 'en',
		];

		// buat temporary json
    	$temp_json = tempnam(self::$output, "json");
    	file_put_contents($temp_json, $json_data);

    	if (!empty(json_decode($json_data)))
    	{
    		if ($data_query != '')
				$driver = ['driver' => 'json', 'data_file' => $temp_json, 'json_query' => $data_query];
			else
				$driver = ['driver' => 'json', 'data_file' => $temp_json];

			$options['db_connection'] = $driver;
    	}

		// copy dulu .jasper ke nama uniq spy ouput file/pdf juga uniq
		$temp_jasper = tempnam(self::$output, 'jasper');
		copy($input, $temp_jasper);

		// register shutdown utk otomatis hapus tmp file, jadi kalau script berhenti tetap dihapus
		register_shutdown_function('unlink', $temp_jasper);
		register_shutdown_function('unlink', $temp_json);

		if (!$debug)
		{
			self::$jasper->process(
			    $temp_jasper,
			    self::$output,
			    $options
			)->execute();
		}
		else
		{
			$x = self::$jasper->process(
			    $temp_jasper,
			    self::$output,
			    $options
			)->output();

			myprint_r($x);
			die;
		}

		self::pdf($temp_jasper);
 	} /*}}}*/
}
