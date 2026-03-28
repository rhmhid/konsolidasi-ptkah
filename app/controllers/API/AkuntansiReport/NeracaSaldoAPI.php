<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NeracaSaldoAPI extends BaseAPIController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaSaldoMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_col = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'top'       => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'right'     => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                'left'      => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ]
        ];

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;

        $total_cols = 4 + ($num_cabang * 4) + ($has_total ? 4 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', "Trial Balance");
        $sheet->mergeCells('A1:'.$last_col_letter.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        $sheet->setCellValue('A2', "Cabang");
        $sheet->setCellValue('B2', ": ".$cabang);
        $sheet->mergeCells('B2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', "Periode");
        $sheet->setCellValue('B3', ": ".$report_month);
        $sheet->mergeCells('B3:'.$last_col_letter.'3');

        $sheet->getStyle('A2:'.$last_col_letter.'4')->getFont()->setBold(true)->setSize(12);

        $c = 1;
        $base_headers = ["No.", "Coacode", "Coaname", "Dr / Cr Position"];
        foreach ($base_headers as $h)
        {
            $let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue($let.'5', $h);
            $sheet->mergeCells($let.'5:'.$let.'6');
            $sheet->getStyle($let.'5:'.$let.'6')->applyFromArray($style_col);
            $sheet->getColumnDimension($let)->setAutoSize(true);
            $c++;
        }

        $draw_group_header = function ($col_start, $title) use ($sheet, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+2);
            $l4 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+3);

            $sheet->setCellValue($l1.'5', $title);
            $sheet->mergeCells($l1.'5:'.$l4.'5');
            $sheet->getStyle($l1.'5:'.$l4.'5')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'6', "Beginning Balance");
            $sheet->setCellValue($l2.'6', "Debet");
            $sheet->setCellValue($l3.'6', "Credit");
            $sheet->setCellValue($l4.'6', "Ending Balance");

            $sheet->getStyle($l1.'6:'.$l4.'6')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
            $sheet->getColumnDimension($l3)->setAutoSize(true);
            $sheet->getColumnDimension($l4)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cabang)
        {
            $draw_group_header($c, $cabang['branch_name']);
            $c += 4;
        }

        if ($has_total) $draw_group_header($c, "Total All Branch");

        $empty_tb = true;
        $data_tb = [];

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];
            $data['sdate']  = $data['year'].'-'.$data['month'].'-01';
            $data['edate']  = $data['year'].'-'.$data['month'].'-'.date('t');

            $rs = NeracaSaldoMdl::list($data);

            $data_db = array();
            while (!$rs->EOF)
            {
                $coacode = $rs->fields['coacode'];
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];

                if (!isset($data_db[$coacode]))
                {
                    $data_db[$coacode] = [
                        'coaid'         => $rs->fields['coaid'],
                        'coaname'       => $rs->fields['coaname'],
                        'default_debet' => $rs->fields['default_debet'],
                        'branches'      => [],
                        'total'         => [
                            'openingbal'    => 0,
                            'debet'         => 0,
                            'credit'        => 0
                        ]
                    ];
                }

                $op = floatval($rs->fields['openingbal']);
                $db = floatval($rs->fields['debet']);
                $cr = floatval($rs->fields['credit']);

                $data_db[$coacode]['branches'][$bc]['openingbal'] = ($data_db[$coacode]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$coacode]['branches'][$bc]['debet'] = ($data_db[$coacode]['branches'][$bc]['debet'] ?? 0) + $db;
                $data_db[$coacode]['branches'][$bc]['credit'] = ($data_db[$coacode]['branches'][$bc]['credit'] ?? 0) + $cr;

                $data_db[$coacode]['total']['openingbal'] += $op;
                $data_db[$coacode]['total']['debet'] += $db;
                $data_db[$coacode]['total']['credit'] += $cr;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);
            $coaid_laba_periode_lalu = Modules::$laba_periode_lalu;

            while (!$rss->EOF)
            {
                if ($rss->fields['coaid'] == $coaid_laba_periode_lalu)
                {
                    $coacode = $rss->fields['coacode'];
                    $bc = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];

                    if (!isset($data_db[$coacode]))
                    {
                        $data_db[$coacode] = [
                            'coaid'         => $rss->fields['coaid'],
                            'coaname'       => $rss->fields['coaname'],
                            'default_debet' => $rss->fields['default_debet'],
                            'branches'      => [],
                            'total'         => [
                                'openingbal'    => 0,
                                'debet'         => 0,
                                'credit'        => 0
                            ]
                        ];
                    }

                    $op = floatval($rss->fields['closingbal']);

                    $data_db[$coacode]['branches'][$bc]['openingbal'] = ($data_db[$coacode]['branches'][$bc]['openingbal'] ?? 0) + $op;
                    $data_db[$coacode]['total']['openingbal'] += $op;
                }

                $rss->MoveNext();
            }

            ksort($data_db);

            $subtotals = ['branches' => [], 'total' => ['debet' => 0, 'credit' => 0]];

            if (!empty($data_db))
            {
                $no = 1;
                $empty_tb = false;
                $row_idx = 7;

                foreach ($data_db as $coacode => $tmp)
                {
                    $c = 1;
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $no);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $coacode);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $tmp['coaname']);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $tmp['default_debet'] == 't' ? 'Dr' : 'Cr');

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $op = floatval($tmp['branches'][$bc]['openingbal'] ?? 0);
                        $db = floatval($tmp['branches'][$bc]['debet'] ?? 0);
                        $cr = floatval($tmp['branches'][$bc]['credit'] ?? 0);

                        $balance = $tmp['default_debet'] == 't' ? ($db - $cr) : ($cr - $db);
                        $balance += $op;

                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $op);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $db);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $cr);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $balance);

                        $subtotals['branches'][$bc]['debet'] = ($subtotals['branches'][$bc]['debet'] ?? 0) + $db;
                        $subtotals['branches'][$bc]['credit'] = ($subtotals['branches'][$bc]['credit'] ?? 0) + $cr;
                    }

                    if ($has_total)
                    {
                        $op = floatval($tmp['total']['openingbal'] ?? 0);
                        $db = floatval($tmp['total']['debet'] ?? 0);
                        $cr = floatval($tmp['total']['credit'] ?? 0);

                        $balance = $tmp['default_debet'] == 't' ? ($db - $cr) : ($cr - $db);
                        $balance += $op;

                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $op);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $db);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $cr);
                        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $balance);

                        $subtotals['total']['debet'] += $db;
                        $subtotals['total']['credit'] += $cr;
                    }

                    $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->applyFromArray($style_row);
                    $no++;
                    $row_idx++;
                }

                $sheet->setCellValue('A'.$row_idx, 'SUBTOTAL');
                $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);

                $c = 5;
                foreach ($data_cabang as $bc => $cabang)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, '');
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals['branches'][$bc]['debet'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals['branches'][$bc]['credit'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, '');
                }

                if ($has_total)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, '');
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals['total']['debet'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals['total']['credit'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, '');
                }

                $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');
                $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->getFont()->setBold(true);
            }
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Neraca Saldo");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Neraca Saldo.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/
}
?>