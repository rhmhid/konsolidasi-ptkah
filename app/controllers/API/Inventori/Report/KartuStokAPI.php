<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KartuStokAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Report/KartuStokMdl');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate'),
            'edate' => get_var('edate'),
            'gid'   => get_var('gid'),
            'mbid'  => get_var('mbid'),
        );

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

        $sheet->setCellValue('A1', "Kartu Stok");
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(16);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' s/d '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $sheet->setCellValue('A2', "Periode");
        $sheet->setCellValue('B2', $periode);
        $sheet->mergeCells('B2:I2');

        $data_gudang = Modules::GetGudang($data['gid']);
        $gudang = $data_gudang['nama_gudang'];

        $sheet->setCellValue('A3', "Gudang");
        $sheet->setCellValue('B3', $gudang);
        $sheet->mergeCells('B3:I3');

        $data_barang = Modules::Getbarang($data['mbid']);
        $barang = $data_barang['kode_brg'].' - '.$data_barang['nama_brg'];

        $sheet->setCellValue('A4', "Barang");
        $sheet->setCellValue('B4', $barang);
        $sheet->mergeCells('B4:I4');

        $sheet->getStyle('A2:I4')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "No.");
        $sheet->setCellValue('B6', "Type Transaksi");
        $sheet->setCellValue('C6', "Tanggal Transaksi");
        $sheet->setCellValue('D6', "Keterangan");
        $sheet->setCellValue('E6', "Awal");
        $sheet->setCellValue('F6', "Masuk");
        $sheet->setCellValue('G6', "Keluar");
        $sheet->setCellValue('H6', "Adjusment");
        $sheet->setCellValue('I6', "Akhir");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:I6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:I')->setAutoSize(true);

        $awal = KartuStokMdl::stock_awal($data);

        $rs = KartuStokMdl::list($data);

        $row_idx = 7;
        $no = 1;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $akhir = $awal + $row->masuk + $row->keluar + $row->adj;

            $sheet->setCellValue('A'.$row_idx, $no);
            $sheet->setCellValue('B'.$row_idx, $row->journal_name);
            $sheet->setCellValue('C'.$row_idx, dbtstamp2stringlong_ina($row->invdate));
            $sheet->setCellValue('D'.$row_idx, $row->detailnote);
            $sheet->setCellValue('E'.$row_idx, floatval($awal));
            $sheet->setCellValue('F'.$row_idx, floatval($row->masuk));
            $sheet->setCellValue('G'.$row_idx, floatval($row->keluar));
            $sheet->setCellValue('H'.$row_idx, floatval($row->adj));
            $sheet->setCellValue('I'.$row_idx, floatval($akhir));

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $sheet->getStyle('A'.$row_idx.':I'.$row_idx)->applyFromArray($style_row);

            $no++;
            $row_idx++;
            $awal = $akhir;

            $rs->MoveNext();
        }
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Kartu Stok");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Kartu Stok.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>