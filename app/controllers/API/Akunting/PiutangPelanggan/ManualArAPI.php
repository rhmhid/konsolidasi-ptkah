<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManualArAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/PiutangPelanggan/ManualArMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'custid'    => get_var('custid'),
            'no_inv'    => get_var('no_inv'),
            'bank_id'   => get_var('bank_id'),
            'pegawai_id'=> get_var('pegawai_id'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = ManualArMdl::list($data, true)->RecordCount();
        $rs = ManualArMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'maid'          => $rs->fields['maid'],
                'ardate'        => dbtstamp2stringlong_ina($rs->fields['ardate']),
                'no_inv'        => $rs->fields['no_inv'],
                'arcode'        => $rs->fields['arcode'],
                'nama_customer' => $rs->fields['nama_customer'],
                'nama_pegawai'  => $rs->fields['nama_pegawai'],
                'bank_nama'     => $rs->fields['bank_nama'],
                'keterangan'    => $rs->fields['keterangan'],
                'amount'        => format_uang($rs->fields['amount'], 2),
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
                'suppid'        => $rs->fields['suppid'],
            );

            $rs->MoveNext();
        }

        $data = array(
            'draw'              => $data['draw'],
            'recordsTotal'      => $jmlbris,
            'recordsFiltered'   => $jmlbris,
            'data'              => $record
        );

        $this->response($data, REST::HTTP_OK);
    } /*}}}*/

    public function form_get () /*{{{*/
    {

        $maid = get_var('maid', 0);
        $AddCoa = '';

        $rsd = ManualArMdl::detail_trans($maid);

        if (!$rsd->EOF)
        {
            $data_head = FieldsToObject($rsd->fields);

            $data_head->ardate = date('d-m-Y H:i', strtotime($data_head->ardate));

            $data_head->duedate = date('d-m-Y', strtotime($data_head->duedate));

            while (!$rsd->EOF)
            {
                $data_db = FieldsToObject($rsd->fields);

                $coaid = $data_db->coaid;
                $amount = floatval($data_db->amount);
                $detailnote = addslashes($data_db->detailnote);

                $AddCoa .= "AddCoa ($coaid, '$detailnote', '$amount')\n";

                $rsd->MoveNext();
            }

            $AddCoa .= "FormatMoney()\n";
            $AddCoa .= "summaryAmount()\n";

        }
        else
        {
            $data_head = New stdClass();

            $data_head->maid = $maid;

            $data_head->apdate = date('d-m-Y H:i');

            $data_head->duedate = date('d-m-Y');

            $data_head->suppid = '';
        }

        $data_cust = Modules::data_customer();
        $cmb_cust = $data_cust->GetMenu2('custid', $data_head->custid, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="custid" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Customer..." ');

        $data_bank = Modules::data_bank_cc();
        $cmb_bank = $data_bank->GetMenu2('bank_id', $data_head->bank_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="bank_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Bank..."');

        $data_karyawan = Modules::data_karyawan();
        $cmb_karyawan = $data_karyawan->GetMenu2('pegawai_id', $data_head->pegawai_id, true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="pegawai_id" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Karyawan..."');

        $data_coa_main = Modules::data_coa();
        $cmb_coa_main = $data_coa_main->GetMenu2('coa_main', '', true, false, 0, 'class="form-select form-select-sm rounded-1 w-100" id="coa_main" data-control="select2" data-allow-clear="true" data-placeholder="Pilih Coa..."');

        $data_coa = Modules::data_coa_manual_ar();

        $row_coa = "::Pilih COA ...";

        while (!$data_coa->EOF)
        {
            $row = FieldsToObject($data_coa->fields);

            $row_coa .= ";".$row->coaid.":".$row->coatid.":".$row->coa;

            $data_coa->MoveNext();
        }

        return view('akunting.piutang_pelanggan.manual_ar.form', compact(
            'data_head',
            'cmb_cust',
            'cmb_bank',
            'cmb_karyawan',
            'cmb_coa_main',
            'row_coa',
            'AddCoa'
        ));
    } /*}}}*/

    public function save_patch ($mytype) /*{{{*/
    {
        $msg = ManualArMdl::save_trans($mytype);

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Disimpan'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function delete_post ($myid) /*{{{*/
    {
        $msg = ManualArMdl::delete_trans($myid);

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Dihapus'
            );
        else
            $dtJSON = array(
                'success'   => false,
                'message'   => $msg
            );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/


    public function template_get () /*{{{*/
    {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_colA = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],

        ];   

        $style_colB = [
            'font' => ['color' => array('rgb' => 'f71000'),], // Set font nya jadi bold
            'alignment' => [
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],

        ];
          $style_col = [
                    'font' => ['bold' => true], // Set font nya jadi bold
                     'borders' => array(
                     'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ),
                     ),
                    'alignment' => [
                        'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                        'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                    ],

                ];

        // Buat header tabel nya
        $sheet->setCellValue('A1', "DOKUMEN IMPORT AR");
        $sheet->setCellValue('A6', "TANDA (*) HARUS DIISI");
        $sheet->setCellValue('A7', "(contoh pengisian disertakan, silahkan diupdate)");
        $sheet->setCellValue('A9', "kode");
        $sheet->setCellValue('B9', "nama");
        $sheet->setCellValue('C9', "inv");
        $sheet->setCellValue('D9', "jml");
        $sheet->setCellValue('E9', "due");
        $sheet->setCellValue('F9', "ket");
        $sheet->setCellValue('G9', "id pegawai / id edc");

        $sheet->setCellValue('A10', "KODE CUSTOMER *");
        $sheet->setCellValue('B10', "NAMA CUSTOMER *");
        $sheet->setCellValue('C10', "NO. INVOICE *");
        $sheet->setCellValue('D10', "JUMLAH *");
        $sheet->setCellValue('E10', "DUE DATE");
        $sheet->setCellValue('F10', "KETERANGAN");
        $sheet->setCellValue('G10', "NIP PEGAWAI / KODE EDC");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A1')->applyFromArray($style_colA);
        $sheet->getStyle('A6')->applyFromArray($style_colB);
        $sheet->getStyle('A7')->applyFromArray($style_colB);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A7:G7');
        $sheet->mergeCells('A8:G8');
        $sheet->getStyle('A10:G10')->applyFromArray($style_col);

        // ameh otomatis set width na
        foreach ($sheet->getColumnIterator() as $column) {
           $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Data Upload Manual AR");

        $sheetpenjamin = $spreadsheet->createSheet();
        $sheetpenjamin = $spreadsheet->getActiveSheet();
        $sheetpenjamin = $spreadsheet->setActiveSheetIndex(1);
        $sheetpenjamin->setTitle("Master Penjamin");
        $sheetpenjamin->setCellValue('A1', "NO");
        $sheetpenjamin->setCellValue('B1', "Kode Customer/Penjamin");
        $sheetpenjamin->setCellValue('C1', "NAMA Customer / PENJAMIN");
        $sheetpenjamin->setCellValue('D1', "CABANG");
        $sheetpenjamin->getStyle('A1:D1')->applyFromArray($style_colA);
        // ameh otomatis set width na

        $data_cust = Modules::data_all_customer();


          $style_colPenjamin = [
                    'font' => ['bold' => false], // Set font nya jadi bold
                     'borders' => array(
                     'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ),
                     ),
                    'alignment' => [
                        'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                    ],

                ];



        $mulai  = 2;
        $nourut =1;
        while (!$data_cust->EOF)
        {
            $row = FieldsToObject($data_cust->fields);

            $sheetpenjamin->setCellValue('A'.$mulai,$nourut);
            $sheetpenjamin->setCellValue('B'.$mulai,$row->kode_customer);
            $sheetpenjamin->setCellValue('C'.$mulai,$row->nama_customer);
            $sheetpenjamin->setCellValue('D'.$mulai,strtoupper($row->branch_name));

            $sheetpenjamin->getStyle('A'.$nourut.':D'.$nourut)->applyFromArray($style_colPenjamin);
            $mulai++;
            $nourut++;
            $data_cust->MoveNext();
        }
            $sheetpenjamin->getStyle('A'.$nourut.':D'.$nourut)->applyFromArray($style_colPenjamin);



        foreach ($sheetpenjamin->getColumnIterator() as $column) {
                   $sheetpenjamin->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheetpenjamin->getDefaultRowDimension()->setRowHeight(-1);




        $sheetEDC = $spreadsheet->createSheet();
        $sheetEDC = $spreadsheet->getActiveSheet();
        $sheetEDC = $spreadsheet->setActiveSheetIndex(2);
        $sheetEDC->setTitle("Master EDC");
        $sheetEDC->setCellValue('A1', "NO");
        $sheetEDC->setCellValue('B1', "Kode EDC");
        $sheetEDC->setCellValue('C1', "NAMA EDC");
        $sheetEDC->getStyle('A1:C1')->applyFromArray($style_colA);
        // ameh otomatis set width na


        $dataEDC = Modules::data_all_bank_cc();


          $style_colEDC = [
                    'font' => ['bold' => false], // Set font nya jadi bold
                     'borders' => array(
                     'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ),
                     ),
                    'alignment' => [
                        'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                    ],

                ];



        $mulai  = 2;
        $nourut =1;
        while (!$dataEDC->EOF)
        {
            $row = FieldsToObject($dataEDC->fields);

            $sheetEDC->setCellValue('A'.$mulai,$nourut);
            $sheetEDC->setCellValue('B'.$mulai,$row->bank_kode);
            $sheetEDC->setCellValue('C'.$mulai,$row->bank_nama);
            $sheetEDC->getStyle('A'.$nourut.':C'.$nourut)->applyFromArray($style_colEDC);

            $mulai++;
            $nourut++;
            $dataEDC->MoveNext();
        }
        $sheetEDC->getStyle('A'.$nourut.':C'.$nourut)->applyFromArray($style_colEDC);

        foreach ($sheetEDC->getColumnIterator() as $column) {
                   $sheetEDC->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheetEDC->getDefaultRowDimension()->setRowHeight(-1);


    $spreadsheet->setActiveSheetIndex(0); // Sets the first sheet as active

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="File Upload Manual AR.xls"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

}
?>
