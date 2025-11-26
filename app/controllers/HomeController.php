<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/BaseController.php';

class HomeController extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public function index ()
    {
        $now = dayname(date('w')).', '.dbtstamp2stringina(date('Y-m-d'));

        return view('home', compact(
            'now'
        ));
    }
}