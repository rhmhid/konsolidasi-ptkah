<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class MigrasiDataAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Konfigurasi/MigrasiDataMdl');
    } /*}}}*/

    public function save_reset_data_patch () /*{{{*/
    {
        $msg = MigrasiDataMdl::save_reset_data();

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

    public function list_data_group_akses_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = MigrasiDataMdl::list_group_akses($data, true)->RecordCount();
        $rs = MigrasiDataMdl::list_group_akses($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $modify_time = $rs->fields['modify_time'] == '' ? '' : dbtstamp2stringlong_ina($rs->fields['modify_time']);

            $record[] = array(
                "rgid"      => $rs->fields['rgid'],
                "role_kode" => $rs->fields['role_kode'],
                "role_name" => $rs->fields['role_name'],
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

    public function save_balance_ledger_tb_patch () /*{{{*/
    {
        // $msg = MigrasiDataMdl::save_reset_data();

        $month = get_var('month');
        $year  = get_var('year');

        // Auto Update ledger_summary
        $msg = Modules::balance_ledger_tb($year, $month, false);

        $dtJSON = array(
            'success'   => true,
            'message'   => $msg
        );

        $this->response($dtJSON, REST::HTTP_CREATED);
    } /*}}}*/

    public function download_file_tb_get () /*{{{*/
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'top'       => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right'     => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left'      => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', "DOKUMEN IMPORT TRIAL BALANCE (MINUS A/R A/P F/A)");
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A5', "NOTE: harus balance antara total debet dan credit");
        $sheet->mergeCells('A5:E5');

        $sheet->setCellValue('A6', "TANDA (*) HARUS DIISI");
        $sheet->mergeCells('A6:E6');

        $sheet->setCellValue('A7', "(contoh pengisian disertakan, silahkan diupdate)");
        $sheet->mergeCells('A7:E7');
        // $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A8', "coacode");
        $sheet->setCellValue('B8', "coaname");
        $sheet->setCellValue('C8', "debet");
        $sheet->setCellValue('D8', "credit");
        $sheet->setCellValue('E8', "detailnote");

        $sheet->setCellValue('A9', "KODE COA (*)");
        $sheet->setCellValue('B9', "NAMA COA");
        $sheet->setCellValue('C9', "DEBET (*)");
        $sheet->setCellValue('D9', "CREDIT (*)");
        $sheet->setCellValue('E9', "KETERANGAN");

        $sheet->getStyle('A9:E9')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A9:E9')->getFont()->setBold(true);

        // Contoh Data
        $sheet->setCellValue('A10', "111000");
        $sheet->setCellValue('B10', "BANK 1");
        $sheet->setCellValue('C10', 1000000);
        $sheet->setCellValue('D10', 0);
        $sheet->setCellValue('E10', "Migrasi");

        $sheet->setCellValue('A11', "112000");
        $sheet->setCellValue('B11', "BANK 2");
        $sheet->setCellValue('C11', 0);
        $sheet->setCellValue('D11', 1000000);
        $sheet->setCellValue('E11', "Migrasi");

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("File Migrasi TB");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="File Migrasi TB.xls"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function save_import_tb_patch () /*{{{*/
    {
        $msg = MigrasiDataMdl::save_import_tb();

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

    public function reset_tb_post () /*{{{*/
    {
        $msg = MigrasiDataMdl::reset_tb();

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

    public function list_data_kategori_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = MigrasiDataMdl::list_data_kategori($data, true)->RecordCount();
        $rs = MigrasiDataMdl::list_data_kategori($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "kbid"          => $rs->fields['kbid'],
                "kode_kategori" => $rs->fields['kode_kategori'],
                "nama_kategori" => $rs->fields['nama_kategori'],
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

    public function list_data_satuan_get () /*{{{*/
    {
        $data = array(
            'draw'      => get_var('draw'),
            'start'     => get_var('start'),
            'length'    => get_var('length'),
        );

        $jmlbris = MigrasiDataMdl::list_data_satuan($data, true)->RecordCount();
        $rs = MigrasiDataMdl::list_data_satuan($data, false, $data['start'], $data['length']);

        while (!$rs->EOF)
        {
            $record[] = array(
                "kode_satuan" => $rs->fields['kode_satuan'],
                "nama_satuan" => $rs->fields['nama_satuan'],
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

    public function download_file_barang_get () /*{{{*/
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'top'       => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right'     => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left'      => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', "DOKUMEN IMPORT BARANG");
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A4', "TANDA (*) HARUS DIISI");
        $sheet->mergeCells('A4:K4');

        $sheet->setCellValue('A5', "(contoh pengisian disertakan, silahkan diupdate)");
        $sheet->mergeCells('A5:K5');

        $sheet->setCellValue('A6', "kode_brg");
        $sheet->setCellValue('B6', "kode_kategori");
        $sheet->setCellValue('C6', "nama_brg");
        $sheet->setCellValue('D6', "kode_satuan");
        $sheet->setCellValue('E6', "isi_kecil");
        $sheet->setCellValue('F6', "kode_satuan_besar");
        $sheet->setCellValue('G6', "harga_beli");
        $sheet->setCellValue('H6', "ppn_beli");
        $sheet->setCellValue('I6', "hna_ppn");
        $sheet->setCellValue('J6', "hna_ppn");
        $sheet->setCellValue('K6', "keterangan");

        $sheet->setCellValue('A7', "KODE BARANG (bila kosong maka auto)");
        $sheet->setCellValue('B7', "KATEGORI (*)");
        $sheet->setCellValue('C7', "NAMA BARANG (*)");
        $sheet->setCellValue('D7', "KODE SATUAN TERKECIL (*)");
        $sheet->setCellValue('E7', "QTY SATUAN KECIL KE BESAR");
        $sheet->setCellValue('F7', "KODE SATUAN BESAR");
        $sheet->setCellValue('G7', "HARGA BELI");
        $sheet->setCellValue('H7', "PPN BELI");
        $sheet->setCellValue('I7', "HNA");
        $sheet->setCellValue('J7', "PPN JUAL");
        $sheet->setCellValue('K7', "KETERANGAN");

        $sheet->getStyle('A7:K7')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A7:K7')->getFont()->setBold(true);

        $default_hna = 2000;
        $default_ppn_beli = dataConfigs('default_ppn_beli');
        $default_hna_ppn = $default_hna + (($default_hna * $default_ppn_beli) / 100);

        // Contoh Data
        $sheet->setCellValue('A8', "ATK0001");
        $sheet->setCellValue('B8', "ALAT KANTOR");
        $sheet->setCellValue('C8', "Pulpen Standard");
        $sheet->setCellValue('D8', "PCS");
        $sheet->setCellValue('E8', 100);
        $sheet->setCellValue('F8', "BOX");
        $sheet->setCellValue('G8', 2000);
        $sheet->setCellValue('H8', $default_ppn_beli);
        $sheet->setCellValue('I8', $default_hna_ppn);
        $sheet->setCellValue('J8', dataConfigs('default_ppn_jual'));

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("File Migrasi Barang");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="File Migrasi Barang.xls"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function save_import_barang_patch () /*{{{*/
    {
        $msg = MigrasiDataMdl::save_import_barang();

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

    public function save_import_stok_patch () /*{{{*/
    {
        $msg = MigrasiDataMdl::save_import_stok();

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


    public function download_file_manual_ap_get () /*{{{*/
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
        $sheet->setCellValue('A1', "DOKUMEN IMPORT AP");
        $sheet->setCellValue('A6', "TANDA (*) HARUS DIISI");
        $sheet->setCellValue('A7', "(contoh pengisian disertakan, silahkan diupdate)");
        $sheet->setCellValue('A9', "kode");
        $sheet->setCellValue('B9', "nama");
        $sheet->setCellValue('C9', "inv");
        $sheet->setCellValue('D9', "jml");
        $sheet->setCellValue('E9', "due");
        $sheet->setCellValue('F9', "faktur_pajak");
        $sheet->setCellValue('G9', "tgl_faktur_pajak");
        $sheet->setCellValue('H9', "keterangan");

        $sheet->setCellValue('A10', "KODE SUPPLIER *");
        $sheet->setCellValue('B10', "NAMA SUPPLIER *");
        $sheet->setCellValue('C10', "NO. INVOICE *");
        $sheet->setCellValue('D10', "JUMLAH *");
        $sheet->setCellValue('E10', "DUE DATE");
        $sheet->setCellValue('F10', "FAKTUR PAJAK");
        $sheet->setCellValue('G10', "TANGGAL FAKTUR PAJAK");
        $sheet->setCellValue('H10', "KETERANGAN");
        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A1')->applyFromArray($style_colA);
        $sheet->getStyle('A6')->applyFromArray($style_colB);
        $sheet->getStyle('A7')->applyFromArray($style_colB);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A7:H7');
        $sheet->mergeCells('A8:H8');
        $sheet->getStyle('A10:H10')->applyFromArray($style_col);

        // ameh otomatis set width na
        foreach ($sheet->getColumnIterator() as $column) {
           $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);


        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("File Migrasi Upload Manual AP");


        $sheetsupplier = $spreadsheet->createSheet();
        $sheetsupplier = $spreadsheet->getActiveSheet();
        $sheetsupplier = $spreadsheet->setActiveSheetIndex(1);
        $sheetsupplier->setTitle("Master Supplier");
        $sheetsupplier->setCellValue('A1', "NO");
        $sheetsupplier->setCellValue('B1', "Kode Supplier");
        $sheetsupplier->setCellValue('C1', "Nama Supplier");
        $sheetsupplier->getStyle('A1:C1')->applyFromArray($style_colA);
        // ameh otomatis set width na

        $data_supp = Modules::data_all_supplier();


          $style_colSupplier = [
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
        while (!$data_supp->EOF)
        {
            $row = FieldsToObject($data_supp->fields);

            $sheetsupplier->setCellValue('A'.$mulai,$nourut);
            $sheetsupplier->setCellValue('B'.$mulai,$row->kode_supp);
            $sheetsupplier->setCellValue('C'.$mulai,$row->nama_supp);

            $sheetsupplier->getStyle('A'.$nourut.':C'.$nourut)->applyFromArray($style_colSupplier);
            $mulai++;
            $nourut++;
            $data_supp->MoveNext();
        }
            $sheetsupplier->getStyle('A'.$nourut.':C'.$nourut)->applyFromArray($style_colSupplier);



        foreach ($sheetsupplier->getColumnIterator() as $column) {
                   $sheetsupplier->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheetsupplier->getDefaultRowDimension()->setRowHeight(-1);



        $sheet = $spreadsheet->setActiveSheetIndex(0);

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="File Migrasi Upload Manual AP.xls"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function save_import_manual_ap_patch () /*{{{*/
    {
        $msg = MigrasiDataMdl::save_manual_ap();

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

}
?>
