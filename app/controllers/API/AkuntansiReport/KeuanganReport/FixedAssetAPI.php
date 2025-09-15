<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class FixedAssetAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/KeuanganReport/FixedAssetMdl');
    }

    public function excel_get()
    {
        $data = array(
            'smonth'            => get_var('smonth', date('m')),
            'syear'             => get_var('syear', date('Y')),
            'facid'             => get_var('facid'),
            'falid'             => get_var('falid'),
            'kode_nama_desc'    => get_var('kode_nama_desc')
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Fixed Asset");

        $style_header = [
            'font'      => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $style_row = [
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $style_bold_border = [
            'font'      => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $periode = monthnamelong($data['smonth']).' '.$data['syear'];

        // Title
        $sheet->setCellValue('A1', 'Laporan Fixed Asset');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->setCellValue('A2', 'Periode : '.$periode);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A1:K2')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A4', 'Kategori Asset');
        $sheet->setCellValue('B4', 'Kode Asset');
        $sheet->setCellValue('C4', 'Nama Asset');
        $sheet->setCellValue('D4', 'Tanggal Efektif');
        $sheet->setCellValue('E4', 'Umur ( Bulan )');
        $sheet->setCellValue('F4', 'Lokasi');
        $sheet->setCellValue('G4', 'Jumlah Penyusutan');
        $sheet->setCellValue('H4', 'Nilai Perolehan');
        $sheet->setCellValue('I4', 'Nilai Minimum');
        $sheet->setCellValue('J4', 'Akumulasi');
        $sheet->setCellValue('K4', 'Nilai Buku');

        $sheet->getStyle('A4:K4')->applyFromArray($style_header);

        foreach (range('A', 'K') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $rs = FixedAssetMdl::list($data);

        $no = 1;
        $rowIdx = 5;
        $tot_perolehan = $tot_minimum = $tot_akumulasi = $tot_buku = 0;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $umur_thn = floor($row->masa_manfaat / 12);
            $umur_bln = ($row->masa_manfaat % 12);
            $masa_manfaat = "";

            if ($umur_thn <> 0) $masa_manfaat .= $umur_thn.' Tahun ';

            if ($umur_bln <> 0) $masa_manfaat .= $umur_bln.' Bulan';

            $nilai_buku = $row->nilai_perolehan - $row->nilai_minimum - $row->akumulasi;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $row->nama_kategori);
            $sheet->setCellValue("B$rowIdx", $row->facode);
            $sheet->setCellValue("C$rowIdx", $row->faname);
            $sheet->setCellValue("D$rowIdx", dbtstamp2stringina($row->fadate));
            $sheet->setCellValue("E$rowIdx", $masa_manfaat);
            $sheet->setCellValue("F$rowIdx", $row->lokasi_nama);
            $sheet->setCellValue("G$rowIdx", $row->dpr_count);
            $sheet->setCellValue("H$rowIdx", floatval($row->nilai_perolehan));
            $sheet->setCellValue("I$rowIdx", floatval($row->nilai_minimum));
            $sheet->setCellValue("J$rowIdx", floatval($row->akumulasi));
            $sheet->setCellValue("K$rowIdx", floatval($nilai_buku));

            $sheet->getStyle("A$rowIdx:K$rowIdx")->applyFromArray($style_row);

            $rowIdx++;
            $tot_perolehan += $row->nilai_perolehan;
            $tot_minimum += $row->nilai_minimum;
            $tot_akumulasi += $row->akumulasi;
            $tot_buku += $nilai_buku;

            $rs->MoveNext();
        }

        // TOTAL
        $sheet->setCellValue("A$rowIdx", 'TOTAL');
        $sheet->mergeCells("A$rowIdx:G$rowIdx");

        $sheet->setCellValue("H$rowIdx", floatval($tot_perolehan));
        $sheet->setCellValue("I$rowIdx", floatval($tot_minimum));
        $sheet->setCellValue("J$rowIdx", floatval($tot_akumulasi));
        $sheet->setCellValue("K$rowIdx", floatval($tot_buku));
        $sheet->getStyle("A$rowIdx:K$rowIdx")->applyFromArray($style_bold_border);

        // Output
        $filename = 'Laporan Fixed Asset.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}