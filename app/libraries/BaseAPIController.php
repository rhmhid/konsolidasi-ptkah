<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST.php';

use Luthier\Auth;

abstract class BaseAPIController extends REST
{
    public function __construct () /*{{{*/
    {
        parent::__construct();
    } /*}}}*/

    public function index () /*{{{*/
    {

    }
}