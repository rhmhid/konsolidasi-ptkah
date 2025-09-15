<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InfoStokAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('Inventori/Report/InfoStokMdl');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'sdate'     => get_var('sdate'),
            'coaid_inv' => get_var('coaid_inv'),
            'kbid'      => get_var('kbid'),
            'gid'       => get_var('gid'),
            'kode_nama' => get_var('kode_nama'),
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

        $sheet->setCellValue('A1', "Informasi Stok ( Stock Status )");
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(16);

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $sheet->setCellValue('A2', "Sampai Dengan Periode ".$periode);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2:I2')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A4', "No.");
        $sheet->setCellValue('B4', "Kode Barang");
        $sheet->setCellValue('C4', "Nama Barang");
        $sheet->setCellValue('D4', "Kode Satuan");
        $sheet->setCellValue('E4', "Kategori Barang");
        $sheet->setCellValue('F4', "Status Barang");
        $sheet->setCellValue('G4', "WAC");
        $sheet->setCellValue('H4', "Stok");
        $sheet->setCellValue('I4', "Amount");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A4:I4')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:I')->setAutoSize(true);

        $rs = InfoStokMdl::list($data);

        $row_idx = 5;
        $no = 1;
        $tot_amount = 0;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $is_aktif = $row->is_aktif == 't' ? 'Aktif' : 'Tidak Aktif';

            $sheet->setCellValue('A'.$row_idx, $no);
            $sheet->setCellValue('B'.$row_idx, $row->kode_brg);
            $sheet->setCellValue('C'.$row_idx, $row->nama_brg);
            $sheet->setCellValue('D'.$row_idx, $row->kode_satuan);
            $sheet->setCellValue('E'.$row_idx, $row->kel_brg);
            $sheet->setCellValue('F'.$row_idx, $is_aktif);
            $sheet->setCellValue('G'.$row_idx, floatval($row->wac));
            $sheet->setCellValue('H'.$row_idx, floatval($row->stock));
            $sheet->setCellValue('I'.$row_idx, floatval($row->amount));

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $sheet->getStyle('A'.$row_idx.':I'.$row_idx)->applyFromArray($style_row);

            $no++;
            $row_idx++;
            $tot_amount += $row->amount;

            $rs->MoveNext();
        }

        $sheet->setCellValue('A'.$row_idx, "TOTAL AMOUNT");
        $sheet->setCellValue('I'.$row_idx, floatval($tot_amount));
        $sheet->mergeCells('A'.$row_idx.':H'.$row_idx);

        $sheet->getStyle('A'.$row_idx.':i'.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');
        $sheet->getStyle('A'.$row_idx.':I'.$row_idx)->getFont()->setBold(true);
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Informasi Stok");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Informasi Stok.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>