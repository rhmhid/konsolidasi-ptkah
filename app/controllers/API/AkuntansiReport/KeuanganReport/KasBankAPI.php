<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class KasBankAPI extends BaseAPIController
{
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/KeuanganReport/KasBankMdl');
    }

    public function excel_get ()
    {
        $data = array(
            'sdate'     => get_var('sdate', date('d-m-Y')),
            'edate'     => get_var('edate', date('d-m-Y')),
            'bank_id'   => get_var('bank_id'),
            'pctid'     => get_var('pctid'),
            'jtid'      => get_var('jtid'),
            'is_posted' => get_var('is_posted')
        );

        $sdate = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate'])));

        $edate = dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Kas & Bank");

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
        $sheet->setCellValue('A1', 'Laporan Kas & Bank');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'PERIODE : '.$sdate.' sd '.$edate);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        $data_bank = Modules::GetBank($data['bank_id']);

        $nama_bank = $data_bank['bank_nama'];

        $sheet->setCellValue('A3', 'Kas / Bank : '.$nama_bank);
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setBold(true);

        // Header
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Tanggal Transaksi');
        $sheet->setCellValue('C5', 'Kode Transaksi');
        $sheet->setCellValue('D5', 'No. Jurnal');
        $sheet->setCellValue('E5', 'Tipe Jurnal');
        $sheet->setCellValue('F5', 'Keterangan');
        $sheet->setCellValue('G5', 'Status Posting');
        $sheet->setCellValue('H5', 'Status Oleh');
        $sheet->setCellValue('I5', 'Nominal');
        $sheet->setCellValue('J5', 'Saldo Akhir');

        $sheet->getStyle('A5:J5')->applyFromArray($style_header);

        foreach (range('A', 'J') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $data['status'] = 'saldo_awal';

        $rs_awal = KasBankMdl::list($data);

        $saldo_awal = $rs_awal->fields['saldo_awal'];

        $data['status'] = '';

        $rs = KasBankMdl::list($data);

        $rowIdx = 6;
        $no = 1;

        if ($rs->EOF)
        {
            $sheet->setCellValue("A$rowIdx", 'Tidak ada data untuk ditampilkan.');
            $sheet->mergeCells("A$rowIdx:J$rowIdx");

            // Set teks rata tengah dan italic
            $style = $sheet->getStyle("A$rowIdx:J$rowIdx");

            $style->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $style->getFont()->setItalic(true);

            $style->applyFromArray($style_row);
        }
        else
        {
            $sheet->setCellValue("A$rowIdx", "");
            $sheet->setCellValue("B$rowIdx", $sdate);
            $sheet->setCellValue("C$rowIdx", "");
            $sheet->mergeCells("C$rowIdx:D$rowIdx");
            $sheet->setCellValue("E$rowIdx", "BEGINING BALANCE");
            $sheet->setCellValue("F$rowIdx", "");
            $sheet->mergeCells("F$rowIdx:I$rowIdx");
            $sheet->setCellValue("J$rowIdx", floatval($saldo_awal));

            $sheet->getStyle("A$rowIdx:J$rowIdx")->applyFromArray($style_row);
            $rowIdx++;

            while (!$rs->EOF)
            {
                $row = FieldsToObject($rs->fields);

                $status_posted = $row->is_posted == 't' ? 'POSTED' : 'NOT POSTED';
                $saldo_akhir = $saldo_awal + $row->nominal;

                // Set data row
                $sheet->setCellValue("A$rowIdx", $no);
                $sheet->setCellValue("B$rowIdx", dbtstamp2stringlong_ina($row->gldate));
                $sheet->setCellValue("C$rowIdx", $row->reff_code);
                $sheet->setCellValue("D$rowIdx", $row->gldoc);
                $sheet->setCellValue("E$rowIdx", $row->journal_name);
                $sheet->setCellValue("F$rowIdx", $row->notes);
                $sheet->setCellValue("G$rowIdx", $status_posted);
                $sheet->setCellValue("H$rowIdx", $row->user_posting);
                $sheet->setCellValue("I$rowIdx", floatval($row->nominal));
                $sheet->setCellValue("J$rowIdx", floatval($saldo_akhir));

                $sheet->getStyle("A$rowIdx:J$rowIdx")->applyFromArray($style_row);

                $no++;
                $rowIdx++;
                $saldo_awal = $saldo_akhir;

                $rs->MoveNext();
            }
        }

        // Output
        $filename = 'Laporan Kas & Bank.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}