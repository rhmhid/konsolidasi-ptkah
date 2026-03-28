<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LabaRugiAPI extends BaseAPIController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/LabaRugiMdl');

        self::$ho_jkk = dataConfigs('default_kode_branch_jkk');
    } /*}}}*/

    public function excel_get ($mytipe) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
        );

        if ($mytipe == 'pl-new') return self::excel_baru($data);
        elseif ($mytipe == 'pl-new-daily') return self::excel_baru_daily($data);
        elseif ($mytipe == 'pl-new-detail') return self::excel_baru_detail($data);
        elseif ($mytipe == 'pl-new-detail-daily') return self::excel_baru_detail_daily($data);

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $data_pl = $subtotals = [];
        $grand_totals = [
            'income' => ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]],
            'cost'   => ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]]
        ];

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $coatid = $rs->fields['coatid'];
                $coaid = $rs->fields['coaid'];

                if (!isset($data_pl[$coatid]))
                {
                    $data_pl[$coatid] = [
                        'headname'  => $coatid == 4 ? 'INCOME' : 'COST',
                        'data'      => []
                    ];
                }

                if (!isset($data_pl[$coatid]['data'][$coaid]))
                {
                    $data_pl[$coatid]['data'][$coaid] = [
                        'coacode' => $rs->fields['coacode'],
                        'coaname' => $rs->fields['coaname'],
                        'posisi'  => $rs->fields['default_debet'] == 't' ? 'Dr' : 'Cr',
                        'branches'=> [],
                        'total'   => [
                            'amount_bln_prev'   => 0,
                            'amount_bln'        => 0,
                            'closingbal'        => 0
                        ]
                    ];
                }

                $amt_prev = floatval($rs->fields['amount_bln_prev']);
                $amt_bln  = floatval($rs->fields['amount_bln']);
                $closing  = floatval($rs->fields['closingbal']);

                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln_prev'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $data_pl[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_pl[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $data_pl[$coatid]['data'][$coaid]['total']['amount_bln_prev'] += $amt_prev;
                $data_pl[$coatid]['data'][$coaid]['total']['amount_bln'] += $amt_bln;
                $data_pl[$coatid]['data'][$coaid]['total']['closingbal'] += $closing;

                $subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] = ($subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $subtotals[$coatid]['branches'][$bc]['amount_bln'] = ($subtotals[$coatid]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $subtotals[$coatid]['branches'][$bc]['closingbal'] = ($subtotals[$coatid]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $subtotals[$coatid]['total']['amount_bln_prev'] = ($subtotals[$coatid]['total']['amount_bln_prev'] ?? 0) + $amt_prev;
                $subtotals[$coatid]['total']['amount_bln'] = ($subtotals[$coatid]['total']['amount_bln'] ?? 0) + $amt_bln;
                $subtotals[$coatid]['total']['closingbal'] = ($subtotals[$coatid]['total']['closingbal'] ?? 0) + $closing;

                $type = $coatid == 4 ? 'income' : 'cost';
                $grand_totals[$type]['branches'][$bc]['amount_bln_prev'] = ($grand_totals[$type]['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                $grand_totals[$type]['branches'][$bc]['amount_bln'] = ($grand_totals[$type]['branches'][$bc]['amount_bln'] ?? 0) + $amt_bln;
                $grand_totals[$type]['branches'][$bc]['closingbal'] = ($grand_totals[$type]['branches'][$bc]['closingbal'] ?? 0) + $closing;

                $grand_totals[$type]['total']['amount_bln_prev'] += $amt_prev;
                $grand_totals[$type]['total']['amount_bln'] += $amt_bln;
                $grand_totals[$type]['total']['closingbal'] += $closing;

                $rs->MoveNext();
            }
        }

        $laba_rugi = ['branches' => [], 'total' => ['amount_bln_prev' => 0, 'amount_bln' => 0, 'closingbal' => 0]];
        foreach ($data_cabang as $bc => $cabang)
        {
            $laba_rugi['branches'][$bc]['amount_bln_prev'] = ($grand_totals['income']['branches'][$bc]['amount_bln_prev'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['amount_bln_prev'] ?? 0);
            $laba_rugi['branches'][$bc]['amount_bln'] = ($grand_totals['income']['branches'][$bc]['amount_bln'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['amount_bln'] ?? 0);
            $laba_rugi['branches'][$bc]['closingbal'] = ($grand_totals['income']['branches'][$bc]['closingbal'] ?? 0) - ($grand_totals['cost']['branches'][$bc]['closingbal'] ?? 0);
        }

        $laba_rugi['total']['amount_bln_prev'] = $grand_totals['income']['total']['amount_bln_prev'] - $grand_totals['cost']['total']['amount_bln_prev'];
        $laba_rugi['total']['amount_bln'] = $grand_totals['income']['total']['amount_bln'] - $grand_totals['cost']['total']['amount_bln'];
        $laba_rugi['total']['closingbal'] = $grand_totals['income']['total']['closingbal'] - $grand_totals['cost']['total']['closingbal'];

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

        $total_cols = 4 + ($num_cabang * 3) + ($has_total ? 3 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', "Profit And Loss");
        $sheet->mergeCells('A1:'.$last_col_letter.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $cabang_text = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        $sheet->setCellValue('A2', "Cabang");
        $sheet->setCellValue('B2', ": ".$cabang_text);
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

        $draw_group_header = function ($col_start, $title) use ($sheet, $bln, $bln_prev, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+2);

            $sheet->setCellValue($l1.'5', $title);
            $sheet->mergeCells($l1.'5:'.$l3.'5');
            $sheet->getStyle($l1.'5:'.$l3.'5')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'6', $bln);
            $sheet->setCellValue($l2.'6', $bln_prev);
            $sheet->setCellValue($l3.'6', "Until ".$bln);

            $sheet->getStyle($l1.'6:'.$l3.'6')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
            $sheet->getColumnDimension($l3)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cbg)
        {
            $draw_group_header($c, $cbg['branch_name']);
            $c += 3;
        }

        if ($has_total)
            $draw_group_header($c, "Total All Branch");

        $row_idx = 7;
        foreach ($data_pl as $coatid => $group)
        {
            $nomor = 1;

            foreach ($group['data'] as $coaid => $row)
            {
                $c = 1;
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $nomor++);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $row['coacode']);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $row['coaname']);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, $row['posisi']);

                foreach ($data_cabang as $bc => $cbg)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['branches'][$bc]['amount_bln'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['branches'][$bc]['amount_bln_prev'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['branches'][$bc]['closingbal'] ?? 0));
                }

                if ($has_total)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['total']['amount_bln'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['total']['amount_bln_prev'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($row['total']['closingbal'] ?? 0));
                }

                $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->applyFromArray($style_row);
                $row_idx++;
            }

            $c = 5;
            $sheet->setCellValue('A'.$row_idx, 'SUBTOTAL '.$group['headname']);
            $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);

            foreach ($data_cabang as $bc => $cbg)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['branches'][$bc]['amount_bln'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['branches'][$bc]['closingbal'] ?? 0));
            }

            if ($has_total)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['total']['amount_bln'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['total']['amount_bln_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($subtotals[$coatid]['total']['closingbal'] ?? 0));
            }

            $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');
            $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->getFont()->setBold(true);

            $row_idx++;

            $sheet->mergeCells('A'.$row_idx.':'.$last_col_letter.$row_idx);

            $row_idx++;
        }

        $c = 5;
        $sheet->setCellValue('A'.$row_idx, 'TOTAL LABA / RUGI');
        $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);

        foreach ($data_cabang as $bc => $cbg)
        {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['branches'][$bc]['amount_bln'] ?? 0));
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['branches'][$bc]['amount_bln_prev'] ?? 0));
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['branches'][$bc]['closingbal'] ?? 0));
        }

        if ($has_total)
        {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['total']['amount_bln'] ?? 0));
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['total']['amount_bln_prev'] ?? 0));
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_idx, floatval($laba_rugi['total']['closingbal'] ?? 0));
        }

        $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');
        $sheet->getStyle('A'.$row_idx.':'.$last_col_letter.$row_idx)->getFont()->setBold(true);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Profit And Loss");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Profit And Loss.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_baru ($data) /*{{{*/
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

        $tgl_cetak = date('Y-m-d');
        
        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
        ];

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;

        $total_cols = 1 + ($num_cabang * 3) + ($has_total ? 3 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:'.$last_col_letter.'4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $c = 1;
        $let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $sheet->setCellValue($let.'6', "Keterangan");
        $sheet->mergeCells($let.'6:'.$let.'7');
        $sheet->getStyle($let.'6:'.$let.'7')->applyFromArray($style_col);
        $sheet->getColumnDimension($let)->setAutoSize(true);
        $c++;

        $draw_group_header = function ($col_start, $title) use ($sheet, $bln, $bln_prev, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+2);

            $sheet->setCellValue($l1.'6', $title);
            $sheet->mergeCells($l1.'6:'.$l3.'6');
            $sheet->getStyle($l1.'6:'.$l3.'6')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'7', $bln);
            $sheet->setCellValue($l2.'7', $bln_prev);
            $sheet->setCellValue($l3.'7', "Until ".$bln);

            $sheet->getStyle($l1.'7:'.$l3.'7')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
            $sheet->getColumnDimension($l3)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cabang)
        {
            $draw_group_header($c, $cabang['branch_name']);
            $c += 3;
        }

        if ($has_total)
            $draw_group_header($c, "Total All Branch");

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pplrid = $rs->fields['pplrid'];

                if ($rs->fields['coatid'] == 5)
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'] * -1;
                    $amount_bln = $rs->fields['amount_bln'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $amount_bln_prev = $rs->fields['amount_bln_prev'];
                    $amount_bln = $rs->fields['amount_bln'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln_prev'] ?? 0) + floatval($amount_bln_prev);
                $data_db[$pplrid]['branches'][$bc]['amount_bln'] = ($data_db[$pplrid]['branches'][$bc]['amount_bln'] ?? 0) + floatval($amount_bln);
                $data_db[$pplrid]['branches'][$bc]['closingbal'] = ($data_db[$pplrid]['branches'][$bc]['closingbal'] ?? 0) + floatval($closingbal);

                $data_db[$pplrid]['total']['amount_bln_prev'] = ($data_db[$pplrid]['total']['amount_bln_prev'] ?? 0) + floatval($amount_bln_prev);
                $data_db[$pplrid]['total']['amount_bln'] = ($data_db[$pplrid]['total']['amount_bln'] ?? 0) + floatval($amount_bln);
                $data_db[$pplrid]['total']['closingbal'] = ($data_db[$pplrid]['total']['closingbal'] ?? 0) + floatval($closingbal);

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos_rekap();

            $row_pos = 8;
            while (!$rs_pos->EOF)
            {
                $pplrid = $rs_pos->fields['pplrid'];
                $row = $data_db[$pplrid] ?? [];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $c = 1;
                $let_nama = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->setCellValue($let_nama.$row_pos, $space.$nama);
                $c++;

                $start_val_col = $c;

                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt_prev = floatval($row['branches'][$bc]['amount_bln_prev'] ?? 0);
                    $amt_bln = floatval($row['branches'][$bc]['amount_bln'] ?? 0);
                    $amt_cls = floatval($row['branches'][$bc]['closingbal'] ?? 0);

                    if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_prev = '';
                        $amt_bln = '';
                        $amt_cls = '';
                    }

                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_bln);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_prev);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_cls);
                }

                if ($has_total)
                {
                    $amt_prev = floatval($row['total']['amount_bln_prev'] ?? 0);
                    $amt_bln = floatval($row['total']['amount_bln'] ?? 0);
                    $amt_cls = floatval($row['total']['closingbal'] ?? 0);

                    if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt_prev = '';
                        $amt_bln = '';
                        $amt_cls = '';
                    }

                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_bln);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_prev);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_cls);
                }

                $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row);

                if ($is_header == 't')
                    $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setBold(true);

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
                    $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
                }

                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pplrid'];

                    foreach ($data_cabang as $bc => $cabang) {
                        $p = $row['branches'][$bc]['amount_bln_prev'] ?? 0;
                        $b = $row['branches'][$bc]['amount_bln'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['amount_bln_prev'] = ($data_db[$parent_id]['branches'][$bc]['amount_bln_prev'] ?? 0) + $p;
                        $data_db[$parent_id]['branches'][$bc]['amount_bln'] = ($data_db[$parent_id]['branches'][$bc]['amount_bln'] ?? 0) + $b;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $tot_p = $row['total']['amount_bln_prev'] ?? 0;
                    $tot_b = $row['total']['amount_bln'] ?? 0;
                    $tot_c = $row['total']['closingbal'] ?? 0;

                    $data_db[$parent_id]['total']['amount_bln_prev'] = ($data_db[$parent_id]['total']['amount_bln_prev'] ?? 0) + $tot_p;
                    $data_db[$parent_id]['total']['amount_bln'] = ($data_db[$parent_id]['total']['amount_bln'] ?? 0) + $tot_b;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_c;
                }

                $row_pos++;

                $rs_pos->MoveNext();
            }
        }

        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $row_pos++;

            $c = 1;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, 'POS LABA/RUGI LAINNYA');
            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_bln'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_bln_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['closingbal'] ?? 0));
            }

            if ($has_total)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_bln'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_bln_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['closingbal'] ?? 0));
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);

            $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
            $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Rekap Rugi - Laba");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Rekap Rugi - Laba.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_baru_detail ($data) /*{{{*/
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

        $tgl_cetak = date('Y-m-d');
        
        if ($data['month'] > 12)
        {
            $data['prev_month'] = $data['month'] - 1;
            $data['prev_year'] = $data['year'];

            $edate = '31'.'-'.$data['month'].'-'.$data['year'];
            $sdate = '31'.'-'.$data['prev_month'].'-'.$data['year'];

            if ($data['prev_month'] == 12)
                $sdate = date("Y-m-t", strtotime($sdate));
        }
        else
        {
            $edate = $data['year'].'-'.$data['month'].'-01';
            $sdate = $data['month'] == 1 ? $edate : date("Y-m-d", strtotime("-1 month", strtotime($edate)));

            $edate = date("Y-m-t", strtotime($edate));
            $sdate = date("Y-m-t", strtotime($sdate));

            $data['prev_month'] = date("n", strtotime($sdate));
            $data['prev_year'] = date("Y", strtotime($sdate));
        }

        if ($data['month'] <= 12)
        {
            $edate = strtoupper(dbtstamp2stringina($edate));
            $bln = monthnamelong($data['month']).' '.$data['year'];
        }
        else
            $bln = $data['month'].'-'.$data['year'];

        if ($data['prev_month'] <= 12)
        {
            $sdate = strtoupper(dbtstamp2stringina($sdate));
            $bln_prev = monthnamelong($data['prev_month']).' '.$data['prev_year'];
        }
        else
            $bln_prev = $data['prev_month'].'-'.$data['prev_year'];

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
        ];

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;

        $total_cols = 1 + ($num_cabang * 3) + ($has_total ? 3 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN DETAIL RUGI / LABA');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:'.$last_col_letter.'4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $c = 1;
        $let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $sheet->setCellValue($let.'6', "Keterangan");
        $sheet->mergeCells($let.'6:'.$let.'7');
        $sheet->getStyle($let.'6:'.$let.'7')->applyFromArray($style_col);
        $sheet->getColumnDimension($let)->setAutoSize(true);
        $c++;

        $draw_group_header = function ($col_start, $title) use ($sheet, $bln, $bln_prev, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+2);

            $sheet->setCellValue($l1.'6', $title);
            $sheet->mergeCells($l1.'6:'.$l3.'6');
            $sheet->getStyle($l1.'6:'.$l3.'6')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'7', $bln);
            $sheet->setCellValue($l2.'7', $bln_prev);
            $sheet->setCellValue($l3.'7', "Until ".$bln);

            $sheet->getStyle($l1.'7:'.$l3.'7')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
            $sheet->getColumnDimension($l3)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cabang)
        {
            $draw_group_header($c, $cabang['branch_name']);
            $c += 3;
        }

        if ($has_total)
            $draw_group_header($c, "Total All Branch");

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pplid = $rs->fields['pplid'];

                if ($rs->fields['coatid'] == 5)
                {
                    $amount_prev = $rs->fields['amount_bln_prev'] * -1;
                    $amount = $rs->fields['amount_bln'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $amount_prev = $rs->fields['amount_bln_prev'];
                    $amount = $rs->fields['amount_bln'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pplid]['branches'][$bc]['amount_prev'] = ($data_db[$pplid]['branches'][$bc]['amount_prev'] ?? 0) + floatval($amount_prev);
                $data_db[$pplid]['branches'][$bc]['amount'] = ($data_db[$pplid]['branches'][$bc]['amount'] ?? 0) + floatval($amount);
                $data_db[$pplid]['branches'][$bc]['closingbal'] = ($data_db[$pplid]['branches'][$bc]['closingbal'] ?? 0) + floatval($closingbal);

                $data_db[$pplid]['total']['amount_prev'] = ($data_db[$pplid]['total']['amount_prev'] ?? 0) + floatval($amount_prev);
                $data_db[$pplid]['total']['amount'] = ($data_db[$pplid]['total']['amount'] ?? 0) + floatval($amount);
                $data_db[$pplid]['total']['closingbal'] = ($data_db[$pplid]['total']['closingbal'] ?? 0) + floatval($closingbal);

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos();

            $row_pos = 8;
            while (!$rs_pos->EOF)
            {
                $pplid = $rs_pos->fields['pplid'];
                $row = $data_db[$pplid] ?? [];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $c = 1;
                $let_nama = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->setCellValue($let_nama.$row_pos, $space.$nama);
                $c++;

                $start_val_col = $c;

                foreach ($data_cabang as $bc => $cabang)
                {
                    $amt      = floatval($row['branches'][$bc]['amount'] ?? 0);
                    $amt_prev = floatval($row['branches'][$bc]['amount_prev'] ?? 0);
                    $amt_cls  = floatval($row['branches'][$bc]['closingbal'] ?? 0);

                    if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt = '';
                        $amt_prev = '';
                        $amt_cls = '';
                    }

                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_prev);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_cls);
                }

                if ($has_total)
                {
                    $amt      = floatval($row['total']['amount'] ?? 0);
                    $amt_prev = floatval($row['total']['amount_prev'] ?? 0);
                    $amt_cls  = floatval($row['total']['closingbal'] ?? 0);

                    if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                    {
                        $amt = '';
                        $amt_prev = '';
                        $amt_cls = '';
                    }

                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_prev);
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_cls);
                }

                $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row);

                if ($is_header == 't')
                    $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setBold(true);

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
                    $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);

                    $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
                }

                if ($rs_pos->fields['parent_pplid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pplid'];
                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $p = $row['branches'][$bc]['amount_prev'] ?? 0;
                        $b = $row['branches'][$bc]['amount'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['amount_prev'] = ($data_db[$parent_id]['branches'][$bc]['amount_prev'] ?? 0) + $p;
                        $data_db[$parent_id]['branches'][$bc]['amount'] = ($data_db[$parent_id]['branches'][$bc]['amount'] ?? 0) + $b;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $tot_p = $row['total']['amount_prev'] ?? 0;
                    $tot_b = $row['total']['amount'] ?? 0;
                    $tot_c = $row['total']['closingbal'] ?? 0;

                    $data_db[$parent_id]['total']['amount_prev'] = ($data_db[$parent_id]['total']['amount_prev'] ?? 0) + $tot_p;
                    $data_db[$parent_id]['total']['amount'] = ($data_db[$parent_id]['total']['amount'] ?? 0) + $tot_b;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_c;
                }

                $row_pos++;

                $rs_pos->MoveNext();
            }
        }

        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $row_pos++;

            $c = 1;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, 'POS LABA/RUGI LAINNYA');
            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['closingbal'] ?? 0));
            }

            if ($has_total)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_prev'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['closingbal'] ?? 0));
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');

            $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
            $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Detail Rugi - Laba");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Detail Rugi - Laba.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_baru_daily ($data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
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

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);
        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
        ];

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;

        $total_cols = 1 + ($num_cabang * 2) + ($has_total ? 2 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA DAILY');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'PER '.strtoupper($sdate).' DAN '.strtoupper($edate));
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:'.$last_col_letter.'4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $c = 1;
        $let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $sheet->setCellValue($let.'6', "Keterangan");
        $sheet->mergeCells($let.'6:'.$let.'7');
        $sheet->getStyle($let.'6:'.$let.'7')->applyFromArray($style_col);
        $sheet->getColumnDimension($let)->setAutoSize(true);
        $c++;

        $draw_group_header = function ($col_start, $title) use ($sheet, $sdate, $edate, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);

            $sheet->setCellValue($l1.'6', $title);
            $sheet->mergeCells($l1.'6:'.$l2.'6');
            $sheet->getStyle($l1.'6:'.$l2.'6')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'7', $sdate." sd ".$edate);
            $sheet->setCellValue($l2.'7', "Until ".$edate);

            $sheet->getStyle($l1.'7:'.$l2.'7')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cabang)
        {
            $draw_group_header($c, $cabang['branch_name']);
            $c += 2;
        }

        if ($has_total)
            $draw_group_header($c, "Total All Branch");

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $pplrid = $rs->fields['pplrid'];

            $amount_period = floatval($rs->fields['amount_period']);
            $amount_untill = floatval($rs->fields['amount_untill']);

            $data_db[$pplrid]['branches'][$bc]['amount_period'] = ($data_db[$pplrid]['branches'][$bc]['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplrid]['branches'][$bc]['amount_untill'] = ($data_db[$pplrid]['branches'][$bc]['amount_untill'] ?? 0) + $amount_untill;

            $data_db[$pplrid]['total']['amount_period'] = ($data_db[$pplrid]['total']['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplrid]['total']['amount_untill'] = ($data_db[$pplrid]['total']['amount_untill'] ?? 0) + $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos_rekap();

        $row_pos = 8;
        while (!$rs_pos->EOF)
        {
            $pplrid = $rs_pos->fields['pplrid'];
            $row = $data_db[$pplrid] ?? [];

            $space = str_repeat("     ", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            $c = 1;
            $let_nama = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue($let_nama.$row_pos, $space.$nama);
            $c++;

            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $amt_per = floatval($row['branches'][$bc]['amount_period'] ?? 0);
                $amt_unt = floatval($row['branches'][$bc]['amount_untill'] ?? 0);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_per);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_unt);
            }

            if ($has_total)
            {
                $amt_per = floatval($row['total']['amount_period'] ?? 0);
                $amt_unt = floatval($row['total']['amount_untill'] ?? 0);

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_per);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_unt);
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row);

            if ($is_header == 't')
                $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setBold(true);

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
                $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
            }

            if ($rs_pos->fields['parent_pplrid'] != '')
            {
                $parent_id = $rs_pos->fields['parent_pplrid'];

                foreach ($data_cabang as $bc => $cabang)
                {
                    $p = $row['branches'][$bc]['amount_period'] ?? 0;
                    $u = $row['branches'][$bc]['amount_untill'] ?? 0;

                    $data_db[$parent_id]['branches'][$bc]['amount_period'] = ($data_db[$parent_id]['branches'][$bc]['amount_period'] ?? 0) + $p;
                    $data_db[$parent_id]['branches'][$bc]['amount_untill'] = ($data_db[$parent_id]['branches'][$bc]['amount_untill'] ?? 0) + $u;
                }

                $tot_p = $row['total']['amount_period'] ?? 0;
                $tot_u = $row['total']['amount_untill'] ?? 0;

                $data_db[$parent_id]['total']['amount_period'] = ($data_db[$parent_id]['total']['amount_period'] ?? 0) + $tot_p;
                $data_db[$parent_id]['total']['amount_untill'] = ($data_db[$parent_id]['total']['amount_untill'] ?? 0) + $tot_u;
            }

            $row_pos++;

            $rs_pos->MoveNext();
        }

        if (isset($data_db[0]['total']['amount_untill']) && $data_db[0]['total']['amount_untill'] <> 0)
        {
            $row_pos++;
            $c = 1;

            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, 'POS LABA/RUGI LAINNYA');
            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_period'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_untill'] ?? 0));
            }

            if ($has_total)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_period'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_untill'] ?? 0));
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);

            $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
            $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Rekap Rugi - Laba Daily");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Rekap Rugi - Laba Daily.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_baru_detail_daily ($data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
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

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        $rs_pos = $data_pos = $data_db = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
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
        ];

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;

        $total_cols = 1 + ($num_cabang * 2) + ($has_total ? 2 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN DETAIL RUGI / LABA DAILY');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'PER '.strtoupper($sdate).' DAN '.strtoupper($edate));
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:'.$last_col_letter.'4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $c = 1;
        $let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $sheet->setCellValue($let.'6', "Keterangan");
        $sheet->mergeCells($let.'6:'.$let.'7');
        $sheet->getStyle($let.'6:'.$let.'7')->applyFromArray($style_col);
        $sheet->getColumnDimension($let)->setAutoSize(true);
        $c++;

        $draw_group_header = function ($col_start, $title) use ($sheet, $sdate, $edate, $style_col)
        {
            $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start);
            $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col_start+1);

            $sheet->setCellValue($l1.'6', $title);
            $sheet->mergeCells($l1.'6:'.$l2.'6');
            $sheet->getStyle($l1.'6:'.$l2.'6')->applyFromArray($style_col);

            $sheet->setCellValue($l1.'7', $sdate." sd ".$edate);
            $sheet->setCellValue($l2.'7', "Until ".$edate);

            $sheet->getStyle($l1.'7:'.$l2.'7')->applyFromArray($style_col);
            $sheet->getColumnDimension($l1)->setAutoSize(true);
            $sheet->getColumnDimension($l2)->setAutoSize(true);
        };

        foreach ($data_cabang as $bc => $cabang)
        {
            $draw_group_header($c, $cabang['branch_name']);
            $c += 2;
        }

        if ($has_total)
            $draw_group_header($c, "Total All Branch");

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
            $pplid = $rs->fields['pplid'];

            $amount_period = floatval($rs->fields['amount_period']);
            $amount_untill = floatval($rs->fields['amount_untill']);

            $data_db[$pplid]['branches'][$bc]['amount_period'] = ($data_db[$pplid]['branches'][$bc]['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplid]['branches'][$bc]['amount_untill'] = ($data_db[$pplid]['branches'][$bc]['amount_untill'] ?? 0) + $amount_untill;

            $data_db[$pplid]['total']['amount_period'] = ($data_db[$pplid]['total']['amount_period'] ?? 0) + $amount_period;
            $data_db[$pplid]['total']['amount_untill'] = ($data_db[$pplid]['total']['amount_untill'] ?? 0) + $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos();

        $row_pos = 8;
        while (!$rs_pos->EOF)
        {
            $pplid = $rs_pos->fields['pplid'];
            $row = $data_db[$pplid] ?? [];

            $space = str_repeat("     ", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            $c = 1;
            $let_nama = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue($let_nama.$row_pos, $space.$nama);
            $c++;

            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $amt_per = floatval($row['branches'][$bc]['amount_period'] ?? 0);
                $amt_unt = floatval($row['branches'][$bc]['amount_untill'] ?? 0);

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_per);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_unt);
            }

            if ($has_total)
            {
                $amt_per = floatval($row['total']['amount_period'] ?? 0);
                $amt_unt = floatval($row['total']['amount_untill'] ?? 0);

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amt_per = '';
                    $amt_unt = '';
                }

                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_per);
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, $amt_unt);
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row);

            if ($is_header == 't')
                $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setBold(true);

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);

                $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);

                $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
            }

            if ($rs_pos->fields['parent_pplid'] != '')
            {
                $parent_id = $rs_pos->fields['parent_pplid'];

                foreach ($data_cabang as $bc => $cabang)
                {
                    $p = $row['branches'][$bc]['amount_period'] ?? 0;
                    $u = $row['branches'][$bc]['amount_untill'] ?? 0;

                    $data_db[$parent_id]['branches'][$bc]['amount_period'] = ($data_db[$parent_id]['branches'][$bc]['amount_period'] ?? 0) + $p;
                    $data_db[$parent_id]['branches'][$bc]['amount_untill'] = ($data_db[$parent_id]['branches'][$bc]['amount_untill'] ?? 0) + $u;
                }

                $tot_p = $row['total']['amount_period'] ?? 0;
                $tot_u = $row['total']['amount_untill'] ?? 0;

                $data_db[$parent_id]['total']['amount_period'] = ($data_db[$parent_id]['total']['amount_period'] ?? 0) + $tot_p;
                $data_db[$parent_id]['total']['amount_untill'] = ($data_db[$parent_id]['total']['amount_untill'] ?? 0) + $tot_u;
            }

            $row_pos++;

            $rs_pos->MoveNext();
        }

        if (isset($data_db[0]['total']['amount_untill']) && $data_db[0]['total']['amount_untill'] <> 0)
        {
            $row_pos++;
            $c = 1;

            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, 'POS LABA/RUGI LAINNYA');
            $start_val_col = $c;

            foreach ($data_cabang as $bc => $cabang)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_period'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['branches'][$bc]['amount_untill'] ?? 0));
            }

            if ($has_total)
            {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_period'] ?? 0));
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$row_pos, floatval($data_db[0]['total']['amount_untill'] ?? 0));
            }

            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos.':'.$last_col_letter.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');

            $start_val_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_val_col);
            $sheet->getStyle($start_val_let.$row_pos.':'.$last_col_letter.$row_pos)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Detail PnL Daily");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Detail Rugi - Laba Daily.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/
}
?>