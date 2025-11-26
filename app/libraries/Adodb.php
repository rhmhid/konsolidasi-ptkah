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

	public function init () /*{{{*/
	{
		return self::$adodb;
	} /*}}}*/
}
?>