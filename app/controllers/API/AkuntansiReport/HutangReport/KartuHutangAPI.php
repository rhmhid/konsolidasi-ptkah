<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class KartuHutangAPI extends BaseAPIController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AkuntansiReport/HutangReport/KartuHutangMdl');
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

        $data_supp = Modules::GetSupplier($data['suppid']);

        $nama_supp = $data_supp['nama_supp'];

        if ($data['suppid'] == -1)
        {
            $data_doctor = Modules::GetPerson($data['doctor_id']);

            $nama_supp .= ' <i style="color: red">[ '.$data_doctor['nama_lengkap'].' ]</i>';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Kartu Hutang");

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
        $sheet->setCellValue('A1', 'Laporan Kartu Hutang');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'PERIODE : '.$sdate.' sd '.$edate);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Supplier dengan RichText
        $richText = new RichText();
        $richText->createTextRun('SUPPLIER : ')->getFont()->setBold(true);

        if ($data['suppid'] == -1)
        {
            $data_doctor = Modules::GetPerson($data['doctor_id']);
            $nama_dokter = '[ ' . $data_doctor['nama_lengkap'] . ' ]';

            $textRun = $richText->createTextRun($nama_dokter);
            $textRun->getFont()->setBold(true)->setItalic(true)->getColor()->setRGB('FF0000');
        }
        else
        {
            $textRun = $richText->createTextRun($nama_supp); // ✅ Bukan createText
            $textRun->getFont()->setBold(true);
        }

        $sheet->setCellValue('A3', $richText);
        $sheet->mergeCells('A3:H3');

        // Header
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Type Trans');
        $sheet->setCellValue('C5', 'Tanggal');
        $sheet->setCellValue('D5', 'Keterangan');
        $sheet->setCellValue('E5', 'Saldo Awal');
        $sheet->setCellValue('F5', 'Nominal Invoice');
        $sheet->setCellValue('G5', 'Nominal Pembayaran');
        $sheet->setCellValue('H5', 'Saldo Akhir');

        $sheet->getStyle('A5:H5')->applyFromArray($style_header);

        foreach (range('A', 'H') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $data['status'] = 'saldo_awal';

        $rs_awal = KartuHutangMdl::list($data);

        $saldo_awal = $rs_awal->fields['saldo_awal'];

        $data['status'] = '';

        $rs = KartuHutangMdl::list($data);

        $rowIdx = 6;
        $no = 1;
        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $saldo_akhir = $saldo_awal + $row->nominal_inv - $row->nominal_pay;

            // Set data row
            $sheet->setCellValue("A$rowIdx", $no++);
            $sheet->setCellValue("B$rowIdx", $row->journal_name);
            $sheet->setCellValue("C$rowIdx", dbtstamp2stringlong_ina($row->gldate));
            $sheet->setCellValue("D$rowIdx", $row->notes);
            $sheet->setCellValue("E$rowIdx", floatval($saldo_awal));
            $sheet->setCellValue("F$rowIdx", floatval($row->nominal_inv));
            $sheet->setCellValue("G$rowIdx", floatval($row->nominal_pay));
            $sheet->setCellValue("H$rowIdx", floatval($saldo_akhir));

            $sheet->getStyle("A$rowIdx:H$rowIdx")->applyFromArray($style_row);

            $no++;
            $rowIdx++;
            $saldo_awal = $saldo_akhir;

            $rs->MoveNext();
        }

        // Output
        $filename = 'Laporan Kartu Hutang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}