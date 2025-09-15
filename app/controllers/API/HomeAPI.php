<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

class HomeAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('HomeMdl');
    } /*}}}*/

    public function index_get () /*{{{*/
    {
        $data = array();
        $data['metaData']['code'] = REST::HTTP_OK;
        $data['metaData']['message'] = 'Hello World';

        // myprint_r($data);
        $this->response($data, REST::HTTP_OK);
    } /*}}}*/
}
?>