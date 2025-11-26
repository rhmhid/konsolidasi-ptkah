<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PenerimaanBarangAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian/Report/PenerimaanBarangMdl');
    }

    public function excel_get()
    {
        $data = array(
            'sdate'         => get_var('sdate'),
            'edate'         => get_var('edate'),
            'gid'           => get_var('gid'),
            'suppid'        => get_var('suppid'),
            'kode_nama_brg' => get_var('kode_nama_brg'),
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Penerimaan Barang");

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

        $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' s/d '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        // Title
        $sheet->setCellValue('A1', 'Laporan Penerimaan Barang');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->setCellValue('A2', 'Periode : ' . $periode);
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A1:L2')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Kode Penerimaan');
        $sheet->setCellValue('C4', 'Tanggal Penerimaan');
        $sheet->setCellValue('D4', 'No. Faktur');
        $sheet->setCellValue('E4', 'Kode PO');
        $sheet->setCellValue('F4', 'Gudang');
        $sheet->setCellValue('G4', 'Supplier');
        $sheet->setCellValue('H4', 'Barang');
        $sheet->setCellValue('I4', 'Jumlah');
        $sheet->setCellValue('J4', 'Harga');
        $sheet->setCellValue('K4', 'Diskon');
        $sheet->setCellValue('L4', 'Subtotal');

        $sheet->getStyle('A4:L4')->applyFromArray($style_header);

        foreach (range('A', 'L') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $rs = PenerimaanBarangMdl::list($data);

        $no = 1;
        $rowIdx = 5;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $barang = $row->kode_brg.' - '.$row->nama_brg;
            $jumlah = floatval($row->vol).' '.$row->nama_satuan;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no++);
            $sheet->setCellValue("B$rowIdx", $row->grcode);
            $sheet->setCellValue("C$rowIdx", dbtstamp2stringlong_ina($row->grdate));
            $sheet->setCellValue("D$rowIdx", $row->no_faktur);
            $sheet->setCellValue("E$rowIdx", $row->pocode);
            $sheet->setCellValue("F$rowIdx", $row->nama_gudang);
            $sheet->setCellValue("G$rowIdx", $row->nama_supp);
            $sheet->setCellValue("H$rowIdx", $barang);
            $sheet->setCellValue("I$rowIdx", $jumlah);
            $sheet->setCellValue("J$rowIdx", floatval($row->harga));
            $sheet->setCellValue("K$rowIdx", floatval($row->disc_rp));
            $sheet->setCellValue("L$rowIdx", floatval($row->subtotal));

            $sheet->getStyle("A$rowIdx:L$rowIdx")->applyFromArray($style_row);
            $rowIdx++;

            $rs->MoveNext();
        }

        // Output
        $filename = 'Laporan Penerimaan Barang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}