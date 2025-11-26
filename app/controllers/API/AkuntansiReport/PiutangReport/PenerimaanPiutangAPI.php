<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PenerimaanPiutangAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/PiutangReport/PenerimaanPiutangMdl');
    }

    public function excel_get()
    {
        $data = array(
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
            'custid'        => get_var('custid'),
            'pegawai_id'    => get_var('pegawai_id'),
            'bank_id'       => get_var('bank_id'),
        );

        $sdate = strtoupper(dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))));

        $edate = strtoupper(dbtstamp2stringina(date('Y-m-d', strtotime($data['edate']))));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Penerimaan Piutang");

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
        $sheet->setCellValue('A1', 'Laporan Penerimaan Piutang');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'PERIODE');
        $sheet->setCellValue('B2', ': '.$sdate.' sd '.$edate);
        $sheet->mergeCells('B2:L2');
        $sheet->getStyle('A2:L2')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Nama Customer');
        $sheet->setCellValue('C4', 'Bank');
        $sheet->setCellValue('D4', 'Cara Terima');
        $sheet->setCellValue('E4', 'Tanggal Terima');
        $sheet->setCellValue('F4', 'No. Terima');
        $sheet->setCellValue('G4', 'Keterangan');
        $sheet->setCellValue('H4', 'Penerimaan');
        $sheet->setCellValue('I4', 'Potongan');
        $sheet->setCellValue('J4', 'Pembulatan');
        $sheet->setCellValue('K4', 'Biaya Lainnya');
        $sheet->setCellValue('L4', 'Subtotal');

        $sheet->getStyle('A4:L4')->applyFromArray($style_header);

        foreach (range('A', 'L') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $rs = PenerimaanPiutangMdl::list($data);

        $rowIdx = 5;
        $no = 1;
        $tot_penerimaan = $tot_potongan = $tot_pembulatan = $tot_other_cost = $tot_all = 0;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $subtotal = $row->penerimaan - $row->potongan + $row->pembulatan + $row->other_cost;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no);

            // RichText untuk customer
            if ($row->custid < 0)
            {
                $richText = new RichText();
                $richText->createTextRun($row->nama_customer . ' ');

                if ($row->custid == -1) $add_nama = $row->nama_lengkap;
                elseif ($row->custid == -2) $add_nama = $row->bn_ar;

                $textRun = $richText->createTextRun('[ ' . $add_nama . ' ]');
                $textRun->getFont()->setItalic(true)->getColor()->setRGB('FF0000');

                $sheet->setCellValue("B$rowIdx", $richText);
            }
            else
                $sheet->setCellValue("B$rowIdx", $row->nama_customer);

            $sheet->setCellValue("C$rowIdx", $row->bank_nama);
            $sheet->setCellValue("D$rowIdx", GetCaraBayar($row->cara_terima));
            $sheet->setCellValue("E$rowIdx", dbtstamp2stringlong_ina($row->paydate));
            $sheet->setCellValue("F$rowIdx", $row->paycode);
            $sheet->setCellValue("G$rowIdx", $row->keterangan);
            $sheet->setCellValue("H$rowIdx", floatval($row->penerimaan));
            $sheet->setCellValue("I$rowIdx", floatval($row->potongan));
            $sheet->setCellValue("J$rowIdx", floatval($row->pembulatan));
            $sheet->setCellValue("K$rowIdx", floatval($row->other_cost));
            $sheet->setCellValue("L$rowIdx", floatval($subtotal));

            $sheet->getStyle("A$rowIdx:L$rowIdx")->applyFromArray($style_row);

            $no++;
            $rowIdx++;
            $tot_penerimaan += $row->penerimaan;
            $tot_potongan += $row->potongan;
            $tot_pembulatan += $row->pembulatan;
            $tot_other_cost += $row->other_cost;
            $tot_all += $subtotal;

            $rs->MoveNext();
        }

        $sheet->setCellValue("A$rowIdx", 'TOTAL');
        $sheet->mergeCells("A$rowIdx:G$rowIdx");
        $sheet->setCellValue("H$rowIdx", floatval($tot_penerimaan));
        $sheet->setCellValue("I$rowIdx", floatval($tot_potongan));
        $sheet->setCellValue("J$rowIdx", floatval($tot_pembulatan));
        $sheet->setCellValue("K$rowIdx", floatval($tot_other_cost));
        $sheet->setCellValue("L$rowIdx", floatval($tot_all));

        $sheet->getStyle("A$rowIdx:L$rowIdx")->applyFromArray($style_row);
        $sheet->getStyle("A$rowIdx:L$rowIdx")->getFont()->setBold(true);

        // Output
        $filename = 'Laporan Penerimaan Piutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}