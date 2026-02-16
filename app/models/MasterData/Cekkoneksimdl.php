<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/DB.php';

class Cekkoneksimdl extends DB
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public static function list () /*{{{*/
    {
     //   if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

        $sql = "SELECT *
                FROM branch
                WHERE is_aktif='t'
                ORDER BY is_primary DESC";
        
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function db1 () /*{{{*/
    {
        if (Auth::user()->pid == SUPER_USER) DB::Debug(true);

	$sql = "SELECT inet_server_addr() as host,current_database(), current_user";;
        $rs = DB::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function db2 () /*{{{*/
    {
        if (Auth::user()->pid == SUPER_USER) DB2::Debug(true);

	$sql = "SELECT inet_server_addr() as host,current_database(), current_user";;
        $rs = DB2::Execute($sql);

        return $rs;
    } /*}}}*/

    public static function db3 () /*{{{*/
    {
        if (Auth::user()->pid == SUPER_USER) DB3::Debug(true);

        $sql = "SELECT inet_server_addr() as host,current_database(), current_user";;
        $rs = DB3::Execute($sql);

        return $rs;
    } /*}}}*/


}
?>
