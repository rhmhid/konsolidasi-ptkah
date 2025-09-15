<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;

class JurnalManualAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Akunting/JurnalManualMdl');
    } /*}}}*/

    public function list_get () /*{{{*/
    {
        $data = array(
            'draw'              => get_var('draw'),
            'jurnal_speriod'    => get_var('jurnal_speriod', date('d-m-Y')),
            'jurnal_eperiod'    => get_var('jurnal_eperiod', date('d-m-Y')),
            'is_posted'         => get_var('is_posted'),
            'gldoc'             => get_var('gldoc'),
            'keterangan'        => get_var('keterangan'),
            'start'             => get_var('start'),
            'length'            => get_var('length'),
        );

        $jmlbris = JurnalManualMdl::list($data, true)->RecordCount();
        $rs = JurnalManualMdl::list($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                'jmid'          => $rs->fields['jmid'],
                'jurnal_date'   => dbtstamp2stringlong_ina($rs->fields['trans_date']),
                'jurnal_doc'    => $rs->fields['gldoc'],
                'keterangan'    => $rs->fields['keterangan'],
                'is_posted'     => $rs->fields['is_posted'],
                'user_input'    => $rs->fields['useri'],
                'glid'          => $rs->fields['glid'],
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

    public function create_get () /*{{{*/
    {
        $jmid = get_var('jmid', 0);

        $data_jm = New stdClass();

        $data_jm->jmid = $jmid;

        $data_coa = JurnalManualMdl::data_coa();

        $row_coa = "::Pilih COA ...";

        while (!$data_coa->EOF)
        {
            $row = FieldsToObject($data_coa->fields);

            $row_coa .= ";".$row->coaid.":".$row->coatid.":".$row->coa;

            $data_coa->MoveNext();
        }

        $data_cost_center = Modules::data_cost_center();

        $row_cost_center = ":Pilih Cost Center ...";

        while (!$data_cost_center->EOF)
        {
            $row = FieldsToObject($data_cost_center->fields);

            $row_cost_center .= ";".$row->pccid.":".$row->pcc;

            $data_cost_center->MoveNext();
        }

        return view('akunting.jurnal_manual.create', compact(
            'data_jm',
            'row_coa',
            'row_cost_center'
        ));
    } /*}}}*/

    public function save_patch ($mytype) /*{{{*/
    {
        $msg = JurnalManualMdl::save_jurnal($mytype);

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

    public function posting_post ($myid) /*{{{*/
    {
        $msg = JurnalManualMdl::posting_jurnal($myid);

        $dtJSON = array();
        if ($msg == 'true')
            $dtJSON = array(
                'success'   => true,
                'message'   => 'Data Berhasil Diposting'
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
        $msg = JurnalManualMdl::delete_jurnal($myid);

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
        $style_col = [
            'font'      => [
                'bold'  => true // Set font nya jadi bold
            ],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => SpreadsheetBorder::BORDER_THIN
                ]
            ]
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle' => SpreadsheetBorder::BORDER_THIN
                ]
            ]
        ];

        // Buat header tabel nya
        $sheet->setCellValue('A1', "COACODE");
        $sheet->setCellValue('B1', "COANAME");
        $sheet->setCellValue('C1', "NOTES");
        $sheet->setCellValue('D1', "DEBET");
        $sheet->setCellValue('E1', "CREDIT");
        $sheet->setCellValue('F1', "CODE COST");
        $sheet->setCellValue('G1', "NAME COST");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A1:G1')->applyFromArray($style_col);

        $sheet->getColumnDimension('A:G')->setAutoSize(true);

        // Tambah sheet kedua
        $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'C.O.A');
        $spreadsheet->addSheet($sheet2, 1); // 1 = posisi index
        $sheet2->setCellValue('A1', 'COACODE');
        $sheet2->setCellValue('B1', 'COANAME');

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet2->getStyle('A1:B1')->applyFromArray($style_col);

        $rs_coa = JurnalManualMdl::data_coa();

        $default_coa = array('113100', '411102', '411301', '411302', '415211', '415311', '416101', '416102', '416105', '411102', '411102', '411102');
        $row_coa = $row_coa2 = 2;
        while (!$rs_coa->EOF)
        {
            if (in_array($rs_coa->fields['coacode'], $default_coa))
            {
                $sheet->setCellValue('A'.$row_coa, $rs_coa->fields['coacode']);
                $sheet->setCellValue('B'.$row_coa, $rs_coa->fields['coaname']);
                $row_coa++;
            }

            $sheet2->setCellValue('A'.$row_coa2, $rs_coa->fields['coacode']);
            $sheet2->setCellValue('B'.$row_coa2, $rs_coa->fields['coaname']);
            
            $sheet2->getStyle("A$row_coa2:B$row_coa2")->applyFromArray($style_row);
            $row_coa2++;

            $rs_coa->MoveNext();
        }

        $sheet2->getColumnDimension('A:B')->setAutoSize(true);

        // ameh otomatis set width na
        foreach ($sheet->getColumnIterator() as $column)
           $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("File Upload Jurnal Manual");

        // Tambah sheet kedua
        $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'COST CENTER');
        $spreadsheet->addSheet($sheet3, 2); // 2 = posisi index
        $sheet3->setCellValue('A1', 'CODE');
        $sheet3->setCellValue('B1', 'PROFIT / COST CENTER');

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet3->getStyle('A1:B1')->applyFromArray($style_col);

        $rs_cost_center = JurnalManualMdl::data_cost_center();

        $row_cost_center = 2;
        while (!$rs_cost_center->EOF)
        {
            $sheet3->setCellValue('A'.$row_cost_center, $rs_cost_center->fields['pcccode']);
            $sheet3->setCellValue('B'.$row_cost_center, $rs_cost_center->fields['pccname']);
            
            $sheet3->getStyle("A$row_cost_center:B$row_cost_center")->applyFromArray($style_row);
            $row_cost_center++;

            $rs_cost_center->MoveNext();
        }

        $sheet3->getColumnDimension('A:B')->setAutoSize(true);

        // Pastikan sheet 1 yang aktif
        $spreadsheet->setActiveSheetIndex(0);

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="File Upload Jurnal Manual.xls"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function parsing_excel_post () /*{{{*/
    {
        $success = false;
        $message = "";
        $data = $data_xls = [];
        $status = "";

        try
        {
            // Cek file
            if (empty($_FILES['chooseFile']['tmp_name']))
            {
                $message = "File tidak ditemukan";
                $status = REST::HTTP_BAD_REQUEST;
            }
            else
            {
                $spreadsheet = IOFactory::load($_FILES['chooseFile']['tmp_name']);

                // Set sheet aktif, misalnya sheet ke-1 (index 0)
                $spreadsheet->setActiveSheetIndex(0);

                $rec = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                // resolve the first line Headers
                array_shift($rec);

                if (empty($rec))
                {
                    $message = "Data Excel kosong.";
                    $status = REST::HTTP_BAD_REQUEST;
                }
                else
                {
                    foreach ($rec as $row)
                    {
                        $coacode = trim($row['A']);
                        $data_coa = DB::Execute("SELECT coaid, coatid FROM m_coa WHERE coacode = ?", [$coacode]);

                        $coaid = (int) ($data_coa->fields['coaid'] ?? 0);
                        if ($coaid <= 0) $coaid = '';

                        $id_cost = '';
                        if ($coaid > 3 && !empty($row['F']))
                        {
                            $id_cost = DB::GetOne("SELECT pccid FROM profit_cost_center WHERE pcccode = ?", [$row['F']]);
                            if (intval($id_cost) == 0) $id_cost = '';
                        }

                        $data_xls[] = [
                            'coaid'  => $coaid,
                            'notes'  => $row['C'] ?? '',
                            'debet'  => floatval($row['D']),
                            'credit' => floatval($row['E']),
                            'pccid'  => $id_cost
                        ];
                    }

                    $success = true;
                    $data = $data_xls;
                    $status = REST::HTTP_OK;
                }
            }
        }
        catch (Exception $e)
        {
            $message = "Terjadi kesalahan saat parsing: " . $e->getMessage();
            $status = REST::HTTP_INTERNAL_SERVER_ERROR;
        }

        $dtJSON = array(
            'success'   => $success,
            'message'   => $message,
            'data'      => $data
        );

        $this->response($dtJSON, $status);
    } /*}}}*/
}
?>