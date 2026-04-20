<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HutangSupplierAPI extends BaseAPIController
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/SummaryReport/HutangSupplierMdl');

        self::$kode_kah = dataConfigs('default_kode_branch_kah');
        self::$kode_rsjk = dataConfigs('default_kode_branch_rsjk');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang')
        );

        $rs = HutangSupplierMdl::list($data);

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12)
            $report_month = monthnamelong($data['month']).' '.$data['year'];
        else
            $report_month = $data['month'].'-'.$data['year'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_header = [
            'font' 		=> [
            	'bold'	=> true
            ],
            'alignment'	=> [
            	'horizontal'	=> Alignment::HORIZONTAL_CENTER,
            	'vertical' 		=> Alignment::VERTICAL_CENTER
            ],
            'borders'	=> [
            	'allBorders'	=> [
            		'borderStyle'	=> SpreadsheetBorder::BORDER_THIN
            	]
            ],
            'fill' 		=> [
            	'fillType'		=> \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            	'startColor'	=> [
            		'argb'	=> 'FFE9E9E9'
            	]
            ]
        ];

        $style_row = [
            'alignment'	=> [
            	'vertical'	=> Alignment::VERTICAL_CENTER
            ],
            'borders' 	=> [
            	'allBorders'	=> [
            		'borderStyle'	=> SpreadsheetBorder::BORDER_THIN
            	]
            ]
        ];

		$sheet->setCellValue('A1', 'SUMMARY REPORT A/P PURCHASING');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'CABANG : ' . strtoupper($cabang));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2:A2')->getFont()->setBold(true);

        $sheet->setCellValue('A3', 'PERIODE : ' . strtoupper($report_month));
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3:A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'No.');
        $sheet->setCellValue('B5', 'Cabang');
        $sheet->setCellValue('C5', 'Begining Balance Total');
        $sheet->setCellValue('D5', 'A/P Purchasing Invoice');
        $sheet->setCellValue('E5', 'A/P Purchasing Payment');
        $sheet->setCellValue('F5', 'Ending Balance');

        $sheet->getStyle('A5:F5')->applyFromArray($style_header);

        $row_pos = 6;
        $no = 1;
        $tot_opbal = $tot_ap_inv = $tot_ap_pay = $tot_closbal = 0;

        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $opbal = floatval($row->opbal ?? 0);
            $ap_inv = floatval($row->ap_inv ?? 0);
            $ap_pay = floatval($row->ap_pay ?? 0);
            $closbal = floatval($row->closbal ?? 0);

            $tot_opbal += $opbal;
            $tot_ap_inv += $ap_inv;
            $tot_ap_pay += $ap_pay;
            $tot_closbal += $closbal;

            $sheet->setCellValue('A'.$row_pos, $no++);
            $sheet->setCellValue('B'.$row_pos, $row->branch_name);
            $sheet->setCellValue('C'.$row_pos, $opbal);
            $sheet->setCellValue('D'.$row_pos, $ap_inv);
            $sheet->setCellValue('E'.$row_pos, $ap_pay);
            $sheet->setCellValue('F'.$row_pos, $closbal);

            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row_pos.':F'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');

            $row_pos++;

            $rs->MoveNext();
        }

        if ($no > 1)
        {
            $sheet->setCellValue('A'.$row_pos, 'TOTAL');
            $sheet->mergeCells('A'.$row_pos.':B'.$row_pos);
            
            $sheet->setCellValue('C'.$row_pos, $tot_opbal);
            $sheet->setCellValue('D'.$row_pos, $tot_ap_inv);
            $sheet->setCellValue('E'.$row_pos, $tot_ap_pay);
            $sheet->setCellValue('F'.$row_pos, $tot_closbal);

            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C'.$row_pos.':F'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9E9E9');
        }
        else
        {
            $sheet->setCellValue('A'.$row_pos, 'Tidak ada data untuk ditampilkan.');
            $sheet->mergeCells('A'.$row_pos.':F'.$row_pos);
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'F') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Summary Report AP Purchasing");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Summary Report AP Purchasing.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_detail_get () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang')
        );

        $rs = HutangSupplierMdl::detail($data);

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12)
            $report_month = monthnamelong($data['month']).' '.$data['year'];
        else
            $report_month = $data['month'].'-'.$data['year'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_header = [
            'font' 		=> [
            	'bold'	=> true
            ],
            'alignment'	=> [
            	'horizontal'	=> Alignment::HORIZONTAL_CENTER,
            	'vertical' 		=> Alignment::VERTICAL_CENTER
            ],
            'borders'	=> [
            	'allBorders'	=> [
            		'borderStyle'	=> SpreadsheetBorder::BORDER_THIN
            	]
            ],
            'fill' 		=> [
            	'fillType'		=> \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            	'startColor'	=> [
            		'argb'	=> 'FFE9E9E9'
            	]
            ]
        ];

        $style_row = [
            'alignment'	=> [
            	'vertical'	=> Alignment::VERTICAL_CENTER
            ],
            'borders' 	=> [
            	'allBorders'	=> [
            		'borderStyle'	=> SpreadsheetBorder::BORDER_THIN
            	]
            ]
        ];

		$sheet->setCellValue('A1', 'SUMMARY REPORT A/P PURCHASING DETAIL');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'CABANG : ' . strtoupper($cabang));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2:A2')->getFont()->setBold(true);

        $sheet->setCellValue('A3', 'PERIODE : ' . strtoupper($report_month));
        $sheet->mergeCells('A3:F3');
        $sheet->getStyle('A3:A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'No.');
        $sheet->setCellValue('B5', 'Nama Supplier');
        $sheet->setCellValue('C5', 'Begining Balance Total');
        $sheet->setCellValue('D5', 'A/P Purchasing Invoice');
        $sheet->setCellValue('E5', 'A/P Purchasing Payment');
        $sheet->setCellValue('F5', 'Ending Balance');

        $sheet->getStyle('A5:F5')->applyFromArray($style_header);

        $row_pos = 6;
        $no = 1;
        $tot_opbal = $tot_ap_inv = $tot_ap_pay = $tot_closbal = 0;

        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $opbal = floatval($row->opbal ?? 0);
            $ap_inv = floatval($row->ap_inv ?? 0);
            $ap_pay = floatval($row->ap_pay ?? 0);
            $closbal = floatval($row->closbal ?? 0);

            $tot_opbal += $opbal;
            $tot_ap_inv += $ap_inv;
            $tot_ap_pay += $ap_pay;
            $tot_closbal += $closbal;

            $sheet->setCellValue('A'.$row_pos, $no++);
            $sheet->setCellValue('B'.$row_pos, $row->nama_supp);
            $sheet->setCellValue('C'.$row_pos, $opbal);
            $sheet->setCellValue('D'.$row_pos, $ap_inv);
            $sheet->setCellValue('E'.$row_pos, $ap_pay);
            $sheet->setCellValue('F'.$row_pos, $closbal);

            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row_pos.':F'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');

            $row_pos++;

            $rs->MoveNext();
        }

        if ($no > 1)
        {
            $sheet->setCellValue('A'.$row_pos, 'TOTAL');
            $sheet->mergeCells('A'.$row_pos.':B'.$row_pos);
            
            $sheet->setCellValue('C'.$row_pos, $tot_opbal);
            $sheet->setCellValue('D'.$row_pos, $tot_ap_inv);
            $sheet->setCellValue('E'.$row_pos, $tot_ap_pay);
            $sheet->setCellValue('F'.$row_pos, $tot_closbal);

            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C'.$row_pos.':F'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9E9E9');
        }
        else
        {
            $sheet->setCellValue('A'.$row_pos, 'Tidak ada data untuk ditampilkan.');
            $sheet->mergeCells('A'.$row_pos.':F'.$row_pos);
            $sheet->getStyle('A'.$row_pos.':F'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'F') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Summary Report AP Purchasing");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Summary Report AP Purchasing.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/
}
?>