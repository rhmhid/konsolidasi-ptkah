<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class AgingHutangAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/HutangReport/AgingHutangMdl');
    }

    public function excel_get()
    {
        $data = [
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'date_by'   => get_var('date_by', 'inv'),
            'suppid'    => get_var('suppid'),
            'doctor_id' => get_var('doctor_id'),
        ];

        $rs = AgingHutangMdl::list($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Aging Hutang");

        $style_header = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $style_row = [
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        $style_bold_border = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders' => ['allBorders' => ['borderStyle' => SpreadsheetBorder::BORDER_THIN]]
        ];

        // Title
        $sheet->setCellValue('A1', 'Laporan Aging Hutang');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->setCellValue('A2', 'Sampai Dengan : ' . $data['sdate']);
        $sheet->mergeCells('A2:N2');

        // Header
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Type Trans');
        $sheet->setCellValue('C4', 'Nama Supplier');
        $sheet->setCellValue('D4', 'No. Invoice');
        $sheet->setCellValue('E4', 'Tgl Invoice');
        $sheet->setCellValue('F4', 'Duedate');
        $sheet->setCellValue('G4', 'Nominal');
        $sheet->mergeCells('H4:N4');
        $sheet->setCellValue('H4', 'Umur Pembayaran');

        $agingHeaders = ["<= 0", "1 - 7", "8 - 14", "15 - 30", "31 - 60", "61 - 90", "> 90"];
        $col = 'H';
        foreach ($agingHeaders as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5');
        $sheet->mergeCells('D4:D5');
        $sheet->mergeCells('E4:E5');
        $sheet->mergeCells('F4:F5');
        $sheet->mergeCells('G4:G5');
        $sheet->getStyle('A4:N5')->applyFromArray($style_header);

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $rowIdx = 6;
        $no = 1;
        $last_group_key = null;
        $last_nama_supp = null;
        $last_nama_dokter = null;
        $subtot = $tot = array_fill(0, 8, 0);

        while (!$rs->EOF) {
            $row = FieldsToObject($rs->fields);
            $is_dokter = $row->suppid == -1 && !empty($row->nama_dokter);
            $current_group_key = $is_dokter ? 'dokter_' . $row->doctor_id : 'supp_' . $row->suppid;

            // Subtotal jika ganti group
            if ($last_group_key !== null && $last_group_key !== $current_group_key) {
                $sheet->mergeCells("A$rowIdx:E$rowIdx");

                if (str_starts_with($last_group_key, 'dokter_')) {
                    $richSubtotal = new RichText();
                    $suppPart = $richSubtotal->createTextRun('SUBTOTAL ' . $last_nama_supp);
                    $suppPart->getFont()->setBold(true);

                    $richSubtotal->createText(' ');
                    $dokterPart = $richSubtotal->createTextRun('[ ' . $last_nama_dokter . ' ]');
                    $dokterPart->getFont()->setBold(true)->setItalic(true)->setColor(new Color(Color::COLOR_RED));
                    $sheet->getCell("A$rowIdx")->setValue($richSubtotal);
                } else {
                    $sheet->setCellValue("A$rowIdx", 'SUBTOTAL ' . $last_nama_supp);
                }

                for ($i = 0; $i <= 7; $i++) {
                    $sheet->setCellValue(chr(71 + $i) . $rowIdx, $subtot[$i] > 0 ? $subtot[$i] : '');
                }

                $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_bold_border);
                $rowIdx++;
                $subtot = array_fill(0, 8, 0);
            }

            $up = intval(trim($row->up, ' days'));
            $aging = array_fill(0, 7, '');
            if ($up <= 0) $aging[0] = $row->nominal;
            elseif ($up < 8) $aging[1] = $row->nominal;
            elseif ($up < 15) $aging[2] = $row->nominal;
            elseif ($up < 31) $aging[3] = $row->nominal;
            elseif ($up < 61) $aging[4] = $row->nominal;
            elseif ($up < 91) $aging[5] = $row->nominal;
            else $aging[6] = $row->nominal;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no++);
            $sheet->setCellValue("B$rowIdx", $row->journal_name);
            $sheet->setCellValue("D$rowIdx", $row->no_inv);
            $sheet->setCellValue("E$rowIdx", $row->apdate);
            $sheet->setCellValue("F$rowIdx", $row->duedate);
            $sheet->setCellValue("G$rowIdx", $row->nominal);

            $richNama = new RichText();
            $richNama->createText($row->nama_supp);
            if ($is_dokter) {
                $richNama->createText(' ');
                $dokterPart = $richNama->createTextRun('[ ' . $row->nama_dokter . ' ]');
                $dokterPart->getFont()->setItalic(true)->setColor(new Color(Color::COLOR_RED));
            }
            $sheet->getCell("C$rowIdx")->setValue($richNama);

            $col = 'H';
            for ($i = 0; $i < 7; $i++) {
                $sheet->setCellValue($col . $rowIdx, $aging[$i]);
                $subtot[$i + 1] += floatval($aging[$i]);
                $tot[$i + 1] += floatval($aging[$i]);
                $col++;
            }

            $subtot[0] += $row->nominal;
            $tot[0] += $row->nominal;

            $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_row);
            $rowIdx++;

            $last_group_key = $current_group_key;
            $last_nama_supp = $row->nama_supp;
            $last_nama_dokter = $row->nama_dokter;
            $rs->MoveNext();
        }

        // Subtotal terakhir
        if ($no > 1) {
            $sheet->mergeCells("A$rowIdx:E$rowIdx");

            if (str_starts_with($last_group_key, 'dokter_')) {
                $richSubtotal = new RichText();
                $suppPart = $richSubtotal->createTextRun('SUBTOTAL ' . $last_nama_supp);
                $suppPart->getFont()->setBold(true);

                $richSubtotal->createText(' ');
                $dokterPart = $richSubtotal->createTextRun('[ ' . $last_nama_dokter . ' ]');
                $dokterPart->getFont()->setBold(true)->setItalic(true)->setColor(new Color(Color::COLOR_RED));
                $sheet->getCell("A$rowIdx")->setValue($richSubtotal);
            } else {
                $sheet->setCellValue("A$rowIdx", 'SUBTOTAL ' . $last_nama_supp);
            }

            for ($i = 0; $i <= 7; $i++) {
                $sheet->setCellValue(chr(71 + $i) . $rowIdx, $subtot[$i] > 0 ? $subtot[$i] : '');
            }
            $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_bold_border);
            $rowIdx++;
        }

        // TOTAL
        $sheet->mergeCells("A$rowIdx:E$rowIdx");
        $sheet->setCellValue("A$rowIdx", 'TOTAL');
        for ($i = 0; $i <= 7; $i++) {
            $sheet->setCellValue(chr(71 + $i) . $rowIdx, $tot[$i] > 0 ? $tot[$i] : '');
        }
        $sheet->getStyle("A$rowIdx:N$rowIdx")->applyFromArray($style_bold_border);

        // Output
        $filename = 'Laporan Aging Hutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}