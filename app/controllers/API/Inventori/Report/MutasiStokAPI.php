<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MutasiStokAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Report/MutasiStokMdl');
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

        $sheet->setCellValue('A1', "Laporan Mutasi Stok");
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1:O1')->getFont()->setBold(true)->setSize(16);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' s/d '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $sheet->setCellValue('A2', "Periode");
        $sheet->setCellValue('B2', $periode);
        $sheet->mergeCells('B2:O2');

        $data_barang = Modules::Getbarang($data['mbid']);
        $barang = $data_barang['kode_brg'].' - '.$data_barang['nama_brg'];

        $sheet->setCellValue('A3', "Barang");
        $sheet->setCellValue('B3', $barang);
        $sheet->mergeCells('B3:O3');

        $sheet->getStyle('A2:O3')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A5', "Tanggal Transaksi");
        $sheet->mergeCells('A5:A6');

        $sheet->setCellValue('B5', "No. Transaksi");
        $sheet->mergeCells('B5:B6');

        $sheet->setCellValue('C5', "Type Transaksi");
        $sheet->mergeCells('C5:C6');

        $sheet->setCellValue('D5', "No. Referensi");
        $sheet->mergeCells('D5:D6');

        $sheet->setCellValue('E5', "User");
        $sheet->mergeCells('E5:E6');

        $sheet->setCellValue('F5', "Satuan");
        $sheet->mergeCells('F5:F6');

        $sheet->setCellValue('G5', "WAC");
        $sheet->mergeCells('G5:G6');

        $sheet->setCellValue('H5', "Gudang");
        $sheet->mergeCells('H5:I5');

        $sheet->setCellValue('J5', "Jumlah");
        $sheet->mergeCells('J5:M5');

        $sheet->setCellValue('N5', "Saldo");
        $sheet->mergeCells('N5:O5');

        $sheet->setCellValue('H6', "Dari");
        $sheet->setCellValue('I6', "Ke");
        $sheet->setCellValue('J6', "Jumlah Keluar");
        $sheet->setCellValue('K6', "Nominal Keluar");
        $sheet->setCellValue('L6', "Jumlah Masuk");
        $sheet->setCellValue('M6', "Nominal Masuk");
        $sheet->setCellValue('N6', "Jumlah");
        $sheet->setCellValue('O6', "Nominal");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A5:O6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:O')->setAutoSize(true);

        $rs_awal = MutasiStokMdl::stock_awal($data);

        $vol_awal = $rs_awal->fields['vol_awal'];
        $amount_awal = $rs_awal->fields['amount_awal'];

        $sheet->setCellValue('A7', 'Saldo Awal');
        $sheet->mergeCells('A7:M7');
        $sheet->getStyle('A7:M7')->getAlignment()->setHorizontal('right');

        $sheet->setCellValue('N7', floatval($vol_awal));
        $sheet->setCellValue('O7', floatval($amount_awal));

        $sheet->getStyle('A7:O7')->applyFromArray($style_row);
        $sheet->getStyle('A7:O7')->getFont()->setBold(true);

        $rs = MutasiStokMdl::list($data);

        $row_idx = 8;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $vol_masuk = $row->vol_masuk ? floatval($row->vol_masuk) : '';
            $amount_masuk = $row->amount_masuk ? floatval($row->amount_masuk) : '';

            $vol_keluar = $row->vol_keluar ? floatval($row->vol_keluar) : '';
            $amount_keluar = $row->amount_keluar ? floatval($row->amount_keluar) : '';

            $vol_akhir = $vol_awal + $row->vol_keluar + $row->vol_masuk;
            $amount_akhir = $amount_awal + $row->amount_keluar + $row->amount_masuk;

            $sheet->setCellValue('A'.$row_idx, dbtstamp2stringlong_ina($row->invdate));
            $sheet->setCellValue('B'.$row_idx, $row->invcode);
            $sheet->setCellValue('C'.$row_idx, $row->journal_name);
            $sheet->setCellValue('D'.$row_idx, $row->reff_code);
            $sheet->setCellValue('E'.$row_idx, $row->user);
            $sheet->setCellValue('F'.$row_idx, $row->kode_satuan);
            $sheet->setCellValue('G'.$row_idx, floatval($row->wac));
            $sheet->setCellValue('H'.$row_idx, $row->pengirim);
            $sheet->setCellValue('I'.$row_idx, $row->penerima);
            $sheet->setCellValue('J'.$row_idx, floatval($vol_masuk));
            $sheet->setCellValue('K'.$row_idx, floatval($amount_masuk));
            $sheet->setCellValue('L'.$row_idx, floatval($vol_keluar));
            $sheet->setCellValue('M'.$row_idx, floatval($amount_keluar));
            $sheet->setCellValue('N'.$row_idx, floatval($vol_akhir));
            $sheet->setCellValue('O'.$row_idx, floatval($amount_akhir));

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $sheet->getStyle('A'.$row_idx.':O'.$row_idx)->applyFromArray($style_row);

            $row_idx++;
            $vol_awal = $vol_akhir;
            $amount_awal = $amount_akhir;

            $rs->MoveNext();
        }

        $sheet->setCellValue('A'.$row_idx, 'Saldo Akhir');
        $sheet->mergeCells('A'.$row_idx.':M'.$row_idx);
        $sheet->getStyle('A'.$row_idx.':M'.$row_idx)->getAlignment()->setHorizontal('right');

        $sheet->setCellValue('N'.$row_idx, floatval($vol_akhir));
        $sheet->setCellValue('O'.$row_idx, floatval($amount_akhir));

        $sheet->getStyle('A'.$row_idx.':O'.$row_idx)->applyFromArray($style_row);
        $sheet->getStyle('A'.$row_idx.':O'.$row_idx)->getFont()->setBold(true);
        
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