<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PembayaranHutangAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/HutangReport/PembayaranHutangMdl');
    }

    public function excel_get()
    {
        $data = [
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'suppid'    => get_var('suppid'),
            'doctor_id' => get_var('doctor_id'),
        ];

        $sdate = strtoupper(dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))));

        $edate = strtoupper(dbtstamp2stringina(date('Y-m-d', strtotime($data['edate']))));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Pembayaran Hutang");

        $style_header = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $style_row = [
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        // Title
        $sheet->setCellValue('A1', 'Laporan Pembayaran Hutang');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'PERIODE : '.$sdate.' sd '.$edate);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Supplier');
        $sheet->setCellValue('C4', 'Bank');
        $sheet->setCellValue('D4', 'Cara Bayar');
        $sheet->setCellValue('E4', 'Tanggal Bayar');
        $sheet->setCellValue('F4', 'No. Bayar');
        $sheet->setCellValue('G4', 'Keterangan');
        $sheet->setCellValue('H4', 'Pembayaran');
        $sheet->setCellValue('I4', 'Add/Less');
        $sheet->setCellValue('K4', 'Potongan');
        $sheet->setCellValue('L4', 'Pembulatan');
        $sheet->setCellValue('M4', 'Other Cost');
        $sheet->setCellValue('N4', 'Subtotal');

        $sheet->setCellValue('I5', 'Debet');
        $sheet->setCellValue('J5', 'credit');

        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:G5');
        $sheet->mergeCells('H4:H5');
        $sheet->mergeCells('I4:J4');
        $sheet->mergeCells('K4:K5');
        $sheet->mergeCells('L4:L5');
        $sheet->mergeCells('M4:M5');
        $sheet->mergeCells('N4:N5');

        $sheet->getStyle('A4:N5')->applyFromArray($style_header);

        foreach (range('A', 'N') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $rs = PembayaranHutangMdl::list($data);

        $rowIdx = 6;
        $no = 1;
        $totpay = $totaldeb = $totalcre = $totpot = $totround = $totother = $totall = 0;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $subtotal = $row->pembayaran + $row->al_debet - $row->al_credit - $row->potongan + $row->pembulatan + $row->other_cost;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no);

            // RichText untuk supplier (jika dokter)
            if ($row->suppid == -1)
            {
                $richText = new RichText();
                $richText->createTextRun($row->nama_supp . ' ');

                $textRun = $richText->createTextRun('[ ' . $row->nama_dokter . ' ]');
                $textRun->getFont()->setItalic(true)->getColor()->setRGB('FF0000');

                $sheet->setCellValue("B$rowIdx", $richText);
            }
            else
                $sheet->setCellValue("B$rowIdx", $row->nama_supp);

            $sheet->setCellValue("C$rowIdx", $row->bank_nama);
            $sheet->setCellValue("D$rowIdx", GetCaraBayar($row->cara_bayar));
            $sheet->setCellValue("E$rowIdx", dbtstamp2stringlong_ina($row->paydate));
            $sheet->setCellValue("F$rowIdx", $row->no_bayar);
            $sheet->setCellValue("G$rowIdx", $row->keterangan);
            $sheet->setCellValue("H$rowIdx", floatval($row->pembayaran));
            $sheet->setCellValue("I$rowIdx", floatval($row->al_debet));
            $sheet->setCellValue("J$rowIdx", floatval($row->al_credit));
            $sheet->setCellValue("K$rowIdx", floatval($row->potongan));
            $sheet->setCellValue("L$rowIdx", floatval($row->pembulatan));
            $sheet->setCellValue("M$rowIdx", floatval($row->other_cost));
            $sheet->setCellValue("N$rowIdx", floatval($subtotal));

            $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_row);

            $no++;
            $rowIdx++;
            $totpay += $row->pembayaran;
            $totaldeb += $row->al_debet;
            $totalcre += $row->al_credit;
            $totpot += $row->potongan;
            $totround += $row->pembulatan;
            $totother += $row->other_cost;
            $totall += $subtotal;

            $rs->MoveNext();
        }

        // Merge kolom A-G dan isi "TOTAL"
        $sheet->mergeCells("A$rowIdx:G$rowIdx");
        $sheet->setCellValue("A$rowIdx", 'TOTAL');

        // Data total
        $sheet->setCellValue("H$rowIdx", floatval($totpay));
        $sheet->setCellValue("I$rowIdx", floatval($totaldeb));
        $sheet->setCellValue("J$rowIdx", floatval($totalcre));
        $sheet->setCellValue("K$rowIdx", floatval($totpot));
        $sheet->setCellValue("L$rowIdx", floatval($totround));
        $sheet->setCellValue("M$rowIdx", floatval($totother));
        $sheet->setCellValue("N$rowIdx", floatval($totall));

        // Style total: bold, right alignment, border
        $style_total = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_total);

        // Output
        $filename = 'Laporan Pembayaran Hutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}