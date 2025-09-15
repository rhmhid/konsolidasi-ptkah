<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class AgingHutangUnbillAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/HutangReport/AgingHutangUnbillMdl');
    }

    public function excel_get()
    {
        $data = [
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
        ];

        $rs = AgingHutangUnbillMdl::list($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Aging Hutang ( Unbill )");

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

        // Title
        $sheet->setCellValue('A1', 'Laporan Aging Hutang ( Unbill )');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->setCellValue('A2', 'Sampai Dengan : ' . $data['sdate']);
        $sheet->mergeCells('A2:N2');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Tanggal Penerimaan');
        $sheet->setCellValue('C4', 'Kode Penerimaan');
        $sheet->setCellValue('D4', 'Nama Supplier');
        $sheet->setCellValue('E4', 'No. Faktur / Surat Jalan');
        $sheet->setCellValue('F4', 'Nominal');

        $sheet->getStyle('A4:F4')->applyFromArray($style_header);

        foreach (range('A', 'F') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $no = 1;
        $rowIdx = 5;
        $totall = 0;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no++);
            $sheet->setCellValue("B$rowIdx", dbtstamp2stringlong_ina($row->grdate));
            $sheet->setCellValue("C$rowIdx", $row->grcode);
            $sheet->setCellValue("D$rowIdx", $row->nama_supp);
            $sheet->setCellValue("E$rowIdx", $row->no_faktur);
            $sheet->setCellValue("F$rowIdx", floatval($row->nominal));

            $sheet->getStyle("A$rowIdx:F$rowIdx")->applyFromArray($style_row);
            $rowIdx++;
            $totall += $row->nominal;

            $rs->MoveNext();
        }

        // TOTAL
        $sheet->setCellValue("A$rowIdx", 'TOTAL');
        $sheet->mergeCells("A$rowIdx:E$rowIdx");
        $sheet->setCellValue("F$rowIdx", floatval($totall));
        $sheet->getStyle("A$rowIdx:F$rowIdx")->applyFromArray($style_bold_border);

        // Output
        $filename = 'Laporan Aging Hutang ( Unbill ).xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}