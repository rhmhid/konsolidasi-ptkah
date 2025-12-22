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

}
?>
