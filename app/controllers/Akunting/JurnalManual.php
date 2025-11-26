<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseController.php';

class JurnalManual extends BaseController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/JurnalManualMdl');
    } /*}}}*/

    public function list () /*{{{*/
    {
        $data_posted = Modules::data_posted();
        $cmb_posted = $data_posted->GetMenu2('', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="sPosted" data-control="select2" data-allow-clear="true" data-placeholder="Pilih..."');

        return view('akunting.jurnal_manual.list', compact(
            'cmb_posted')
        );
    } /*}}}*/
}
?>