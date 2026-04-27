<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NeracaAPI extends BaseAPIController
{
    static $ho_jkk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaMdl');

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

        if ($mytipe == 'bs-new') return self::excel_baru($data);
        elseif ($mytipe == 'bs-new-detail') return self::excel_baru_detail($data);

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $data_db = [];

        $totals = [
            'asset'     => ['branches' => [], 'total' => 0],
            'liability' => ['branches' => [], 'total' => 0],
            'equity'    => ['branches' => [], 'total' => 0]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;
        $cols_per_side = 4 + ($num_cabang * 2) + ($has_total ? 2 : 0);

        $start_aktiva = 1;
        $start_pasiva = $cols_per_side + 2;

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

        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_pasiva + $cols_per_side - 1);

        $sheet->setCellValue('A1', "Balance Sheet - Monthly");
        $sheet->mergeCells('A1:'.$last_col_letter.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        $sheet->setCellValue('A2', "Cabang");
        $sheet->setCellValue('B2', ": ".$cabang);
        $sheet->mergeCells('B2:E2');

        $sheet->setCellValue('A3', "Accounting Periode");
        $sheet->mergeCells('B3:E3');

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        $sheet->setCellValue('A4', "Report Month");
        $sheet->setCellValue('B4', ": ".$report_month);
        $sheet->mergeCells('B4:E4');

        $sheet->getStyle('A2:E4')->getFont()->setBold(true)->setSize(12);

        $draw_header = function ($start_col) use ($sheet, $data_cabang, $style_col, $has_total)
        {
            $c = $start_col;
            $headers = ["No.", "Coacode", "Coaname", "Dr / Cr Position"];

            foreach ($headers as $h)
            {
                $l = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->setCellValue($l.'6', $h);
                $sheet->mergeCells($l.'6:'.$l.'7');
                $sheet->getStyle($l.'6:'.$l.'7')->applyFromArray($style_col);
                $sheet->getColumnDimension($l)->setAutoSize(true);
                $c++;
            }

            foreach ($data_cabang as $bc => $cabang)
            {
                $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($l1.'6', $cabang['branch_name']);
                $sheet->mergeCells($l1.'6:'.$l2.'6');
                $sheet->setCellValue($l1.'7', 'Openning Balance');
                $sheet->setCellValue($l2.'7', 'Closing Balance');

                $sheet->getStyle($l1.'6:'.$l2.'7')->applyFromArray($style_col);
                $sheet->getColumnDimension($l1)->setAutoSize(true);
                $sheet->getColumnDimension($l2)->setAutoSize(true);
                $c += 2;
            }

            if ($has_total)
            {
                $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($l1.'6', "Total All Branch");
                $sheet->mergeCells($l1.'6:'.$l2.'6');
                $sheet->setCellValue($l1.'7', 'Openning Balance');
                $sheet->setCellValue($l2.'7', 'Closing Balance');

                $sheet->getStyle($l1.'6:'.$l2.'7')->applyFromArray($style_col);
                $sheet->getColumnDimension($l1)->setAutoSize(true);
                $sheet->getColumnDimension($l2)->setAutoSize(true);
            }
        };

        $draw_header($start_aktiva);
        $draw_header($start_pasiva);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $sheet->setCellValue('B3', ": ".$rs_period->UserDate($data['pbegin'], 'M Y').' - '.$rs_period->UserDate($data['pend'], 'M Y'));

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $coaid = $rs->fields['coaid'];
                $coatid = $rs->fields['coatid'];
                $op = floatval($rs->fields['openingbal']);
                $cl = floatval($rs->fields['closingbal']);

                if (!isset($data_db[$coatid]['data'][$coaid]))
                {
                    $data_db[$coatid]['coatype'] = $rs->fields['coatype'];

                    $data_db[$coatid]['data'][$coaid] = [
                        'coacode'       => $rs->fields['coacode'],
                        'coaname'       => $rs->fields['coaname'],
                        'default_debet' => $rs->fields['default_debet'],
                        'branches'      => [],
                        'total'         => [
                            'openingbal' => 0,
                            'closingbal' => 0
                        ]
                    ];
                }

                $data_db[$coatid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$coatid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$coatid]['data'][$coaid]['total']['openingbal'] += $op;
                $data_db[$coatid]['data'][$coaid]['total']['closingbal'] += $cl;

                if ($coatid == 1)
                {
                    if ($rs->fields['default_debet'] == 'f')
                    {
                        $totals['asset']['branches'][$bc] = ($totals['asset']['branches'][$bc] ?? 0) - $cl;
                        $totals['asset']['total'] -= $cl;
                    }
                    else
                    {
                        $totals['asset']['branches'][$bc] = ($totals['asset']['branches'][$bc] ?? 0) + $cl;
                        $totals['asset']['total'] += $cl;
                    }
                }
                elseif ($coatid == 2)
                {
                    $totals['liability']['branches'][$bc] = ($totals['liability']['branches'][$bc] ?? 0) + $cl;
                    $totals['liability']['total'] += $cl;
                }
                elseif ($coatid == 3)
                {
                    $totals['equity']['branches'][$bc] = ($totals['equity']['branches'][$bc] ?? 0) + $cl;
                    $totals['equity']['total'] += $cl;
                }

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $bc = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];
                $coaid = $rss->fields['coaid'];
                $coatid = $rss->fields['coatid'];
                $op = floatval($rss->fields['openingbal']);
                $cl = floatval($rss->fields['closingbal']);

                if (!isset($data_db[$coatid]['data'][$coaid]))
                {
                    $data_db[$coatid]['coatype'] = $rss->fields['coatype'];

                    $data_db[$coatid]['data'][$coaid] = [
                        'coacode'       => $rss->fields['coacode'],
                        'coaname'       => $rss->fields['coaname'],
                        'default_debet' => $rss->fields['default_debet'],
                        'branches'      => [],
                        'total'         => [
                            'openingbal' => 0,
                            'closingbal' => 0
                        ]
                    ];
                }

                $data_db[$coatid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$coatid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$coatid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$coatid]['data'][$coaid]['total']['openingbal'] += $op;
                $data_db[$coatid]['data'][$coaid]['total']['closingbal'] += $cl;

                $totals['equity']['branches'][$bc] = ($totals['equity']['branches'][$bc] ?? 0) + $cl;
                $totals['equity']['total'] += $cl;

                $rss->MoveNext();
            }
        }

        $data_bs = array(
            '1'   => 'ASSET',
            '1.1' => '',
            '1.2' => 'TOTAL ASSET',
            '2'   => 'LIABILITY',
            '2.1' => '',
            '2.2' => 'TOTAL LIABILITY',
            '2.3' => '',
            '3'   => 'EQUITY',
            '3.1' => '',
            '3.2' => 'TOTAL EQUITY',
            '3.3' => '',
            '3.4' => 'TOTAL LIABILITY + EQUITY'
        );

        $row_aktiva = $row_pasiva = 8;

        $print_row = function ($curr_row, $start_col, $no, $code, $name, $pos, $data_row, $is_bold = false) use ($sheet, $data_cabang, $has_total, $style_row, $cols_per_side)
        {
            $c = $start_col;

            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, $no);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, $code);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, $name);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, $pos);

            if ($data_row !== null)
            {
                foreach ($data_cabang as $bc => $cabang)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($data_row['branches'][$bc]['openingbal'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($data_row['branches'][$bc]['closingbal'] ?? 0));
                }

                if ($has_total)
                {
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($data_row['total']['openingbal'] ?? 0));
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($data_row['total']['closingbal'] ?? 0));
                }
            }

            $end_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + $cols_per_side - 1);
            $start_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col);

            $sheet->getStyle($start_let.$curr_row.':'.$end_let.$curr_row)->applyFromArray($style_row);

            if ($is_bold)
                $sheet->getStyle($start_let.$curr_row.':'.$end_let.$curr_row)->getFont()->setBold(true);
        };

        $print_totals = function ($curr_row, $start_col, $label, $totals_obj) use ($sheet, $data_cabang, $has_total, $style_row, $cols_per_side)
        {
            $c = $start_col;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, '');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, $label);
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, '');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, '');

            if ($totals_obj !== null)
            {
                foreach ($data_cabang as $bc => $cabang)
                {
                    $c++;
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($totals_obj['branches'][$bc] ?? 0));
                }

                if ($has_total)
                {
                    $c++;
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c++).$curr_row, floatval($totals_obj['total'] ?? 0));
                }
            }

            $end_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + $cols_per_side - 1);
            $start_let = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col);
            $sheet->getStyle($start_let.$curr_row.':'.$end_let.$curr_row)->applyFromArray($style_row)->getFont()->setBold(true);
        };

        foreach ($data_bs as $id => $val)
        {
            $no = 1;

            if ($id < 2) // AKTIVA
            {
                if ($id == 1.1 && !empty($data_db[1]['data']))
                {
                    foreach ($data_db[1]['data'] as $key => $tmp)
                    {
                        $posisi = $tmp['default_debet'] == 't' ? 'Dr' : 'Cr';
                        $print_row($row_aktiva, $start_aktiva, $no, $tmp['coacode'], $tmp['coaname'], $posisi, $tmp);
                        $row_aktiva++;
                        $no++;
                    }
                }
                else
                {
                    $obj_total = null;
                    if ($id == 1.2) $obj_total = $totals['asset'];

                    $print_totals($row_aktiva, $start_aktiva, $val, $obj_total);
                    $row_aktiva++;
                }
            }
            else // PASIVA
            {
                $mycoatid = substr($id, 0, 1);

                if (($id == 2.1 || $id == 3.1) && !empty($data_db[$mycoatid]['data']))
                {
                    foreach ($data_db[$mycoatid]['data'] as $key => $tmp)
                    {
                        $posisi = $tmp['default_debet'] == 't' ? 'Dr' : 'Cr';
                        $print_row($row_pasiva, $start_pasiva, $no, $tmp['coacode'], $tmp['coaname'], $posisi, $tmp);
                        $row_pasiva++;
                        $no++;
                    }
                }
                else
                {
                    $obj_total = null;
                    if ($id == 2.2)
                        $obj_total = $totals['liability'];
                    elseif ($id == 3.2)
                        $obj_total = $totals['equity'];
                    elseif ($id == 3.4)
                    {
                        // Kalkulasi Total Liability + Equity
                        $obj_total = ['branches' => [], 'total' => 0];

                        foreach ($data_cabang as $bc => $cabang)
                            $obj_total['branches'][$bc] = ($totals['liability']['branches'][$bc] ?? 0) + ($totals['equity']['branches'][$bc] ?? 0);

                        $obj_total['total'] = $totals['liability']['total'] + $totals['equity']['total'];
                    }

                    $print_totals($row_pasiva, $start_pasiva, $val, $obj_total);
                    $row_pasiva++;
                }
            }
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Balance Sheet");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Balance Sheet.xlsx"');
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
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

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
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;
        $cols_per_side = 1 + ($num_cabang * 2) + ($has_total ? 2 : 0);
        
        $start_aktiva = 1;
        $start_pasiva = $cols_per_side + 2;

        $style_col = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_pasiva + $cols_per_side - 1);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN POSISI KEUANGAN');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:E4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $draw_header = function ($start_col, $title) use ($sheet, $data_cabang, $bln, $bln_prev, $style_col, $has_total)
        {
            $col = $start_col;

            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($letter.'6', "Keterangan");
            $sheet->mergeCells($letter.'6:'.$letter.'7');
            $sheet->getStyle($letter.'6')->applyFromArray($style_col);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
            $col++;

            foreach ($data_cabang as $bc => $cabang)
            {
                $letter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $letter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+1);

                $sheet->setCellValue($letter1.'6', $cabang['branch_name']);
                $sheet->mergeCells($letter1.'6:'.$letter2.'6');
                $sheet->getStyle($letter1.'6:'.$letter2.'6')->applyFromArray($style_col);

                $sheet->setCellValue($letter1.'7', $bln);
                $sheet->setCellValue($letter2.'7', $bln_prev);
                $sheet->getStyle($letter1.'7:'.$letter2.'7')->applyFromArray($style_col);

                $sheet->getColumnDimension($letter1)->setAutoSize(true);
                $sheet->getColumnDimension($letter2)->setAutoSize(true);
                $col += 2;
            }

            if ($has_total)
            {
                $letter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $letter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+1);

                $sheet->setCellValue($letter1.'6', "Total All Branch");
                $sheet->mergeCells($letter1.'6:'.$letter2.'6');
                $sheet->getStyle($letter1.'6:'.$letter2.'6')->applyFromArray($style_col);

                $sheet->setCellValue($letter1.'7', $bln);
                $sheet->setCellValue($letter2.'7', $bln_prev);
                $sheet->getStyle($letter1.'7:'.$letter2.'7')->applyFromArray($style_col);

                $sheet->getColumnDimension($letter1)->setAutoSize(true);
                $sheet->getColumnDimension($letter2)->setAutoSize(true);
            }
        };

        $draw_header($start_aktiva, "Aktiva");

        $draw_header($start_pasiva, "Pasiva");

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pnid = $rs->fields['pnid'];

                if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
                {
                    $openingbal = $rs->fields['openingbal'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $openingbal = $rs->fields['openingbal'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $closingbal;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $bc = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];
                $pnid = $rss->fields['pnid'];
                $op = $rss->fields['openingbal'];
                $cl = $rss->fields['closingbal'];

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $cl;

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            $row_aktiva = $row_pasive = 8;

            while (!$rs_pos->EOF)
            {
                $pnid = $rs_pos->fields['pnid'];
                $row = $data_db[$pnid] ?? [];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $is_aktiva = ($rs_pos->fields['jenis_pos'] == 1);
                $is_pasiva = ($rs_pos->fields['jenis_pos'] == 2);

                $curr_row = $is_aktiva ? $row_aktiva : ($is_pasiva ? $row_pasive : 0);
                $start_col = $is_aktiva ? $start_aktiva : ($is_pasiva ? $start_pasiva : 0);

                if ($curr_row > 0)
                {
                    $c = $start_col;
                    $col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);

                    $sheet->setCellValue($col_letter.$curr_row, $space.$nama);
                    $c++;

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $amt = floatval($row['branches'][$bc]['closingbal'] ?? 0);
                        $amt_prev = floatval($row['branches'][$bc]['openingbal'] ?? 0);

                        if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                        {
                            $amt = '';
                            $amt_prev = '';
                        }

                        $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                        $sheet->setCellValue($let1.$curr_row, $amt);
                        $sheet->setCellValue($let2.$curr_row, $amt_prev);
                        $c += 2;
                    }

                    if ($has_total)
                    {
                        $amt_tot = floatval($row['total']['closingbal'] ?? 0);
                        $amt_tot_prev = floatval($row['total']['openingbal'] ?? 0);

                        if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                        {
                            $amt_tot = '';
                            $amt_tot_prev = '';
                        }

                        $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                        $sheet->setCellValue($let1.$curr_row, $amt_tot);
                        $sheet->setCellValue($let2.$curr_row, $amt_tot_prev);
                    }

                    $end_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + $cols_per_side - 1);
                    $sheet->getStyle($col_letter.$curr_row.':'.$end_col_letter.$curr_row)->applyFromArray($style_row);

                    if ($is_header == 't')
                        $sheet->getStyle($col_letter.$curr_row.':'.$end_col_letter.$curr_row)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $start_val_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + 1);
                        $sheet->getStyle($start_val_letter.$curr_row.':'.$end_col_letter.$curr_row)->getFont()->setUnderline(true);
                    }

                    $curr_row++;

                    if ($is_aktiva) $row_aktiva = $curr_row;
                    if ($is_pasiva) $row_pasive = $curr_row;
                }

                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pnid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $op = $row['branches'][$bc]['openingbal'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['openingbal'] = ($data_db[$parent_id]['branches'][$bc]['openingbal'] ?? 0) + $op;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $tot_op = $row['total']['openingbal'] ?? 0;
                    $tot_cl = $row['total']['closingbal'] ?? 0;

                    $data_db[$parent_id]['total']['openingbal'] = ($data_db[$parent_id]['total']['openingbal'] ?? 0) + $tot_op;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_cl;
                }

                $rs_pos->MoveNext();
            }
        }

        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $row_aktiva++;

            $c = $start_aktiva;
            $col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);

            $sheet->setCellValue($col_letter.$row_aktiva, 'POS NERACA LAINNYA');
            $c++;

            foreach ($data_cabang as $bc => $cabang)
            {
                $amt = floatval($data_db[0]['branches'][$bc]['closingbal'] ?? 0);
                $amt_prev = floatval($data_db[0]['branches'][$bc]['openingbal'] ?? 0);

                $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($let1.$row_aktiva, $amt);
                $sheet->setCellValue($let2.$row_aktiva, $amt_prev);
                $c += 2;
            }

            if ($has_total)
            {
                $amt_tot = floatval($data_db[0]['total']['closingbal'] ?? 0);
                $amt_tot_prev = floatval($data_db[0]['total']['openingbal'] ?? 0);

                $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($let1.$row_aktiva, $amt_tot);
                $sheet->setCellValue($let2.$row_aktiva, $amt_tot_prev);
            }

            $end_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_aktiva + $cols_per_side - 1);
            $start_val_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_aktiva + 1);

            $sheet->getStyle($col_letter.$row_aktiva.':'.$end_col_letter.$row_aktiva)->applyFromArray($style_row)->getFont()->setBold(true);
            $sheet->getStyle($start_val_letter.$row_aktiva.':'.$end_col_letter.$row_aktiva)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Posisi Keuangan");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Posisi Keuangan.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/

    public function excel_baru_detail ($data) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
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
            $sdate = date("Y-m-d", strtotime("-1 month", strtotime($edate)));

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
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $num_cabang = count($data_cabang);
        $has_total = $num_cabang > 1;
        $cols_per_side = 1 + ($num_cabang * 2) + ($has_total ? 2 : 0);
        
        $start_aktiva = 1;
        $start_pasiva = $cols_per_side + 2;

        $style_col = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_pasiva + $cols_per_side - 1);

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');

        $sheet->setCellValue('A2', 'LAPORAN POSISI KEUANGAN');
        $sheet->mergeCells('A2:'.$last_col_letter.'2');

        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:'.$last_col_letter.'3');

        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');

        $sheet->getStyle('A4:E4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $draw_header = function ($start_col, $title) use ($sheet, $data_cabang, $bln, $bln_prev, $style_col, $has_total)
        {
            $col = $start_col;

            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($letter.'6', "Keterangan");
            $sheet->mergeCells($letter.'6:'.$letter.'7');
            $sheet->getStyle($letter.'6')->applyFromArray($style_col);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
            $col++;

            foreach ($data_cabang as $bc => $cabang)
            {
                $letter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $letter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+1);

                $sheet->setCellValue($letter1.'6', $cabang['branch_name']);
                $sheet->mergeCells($letter1.'6:'.$letter2.'6');
                $sheet->getStyle($letter1.'6:'.$letter2.'6')->applyFromArray($style_col);

                $sheet->setCellValue($letter1.'7', $bln);
                $sheet->setCellValue($letter2.'7', $bln_prev);
                $sheet->getStyle($letter1.'7:'.$letter2.'7')->applyFromArray($style_col);

                $sheet->getColumnDimension($letter1)->setAutoSize(true);
                $sheet->getColumnDimension($letter2)->setAutoSize(true);
                $col += 2;
            }

            if ($has_total)
            {
                $letter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $letter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col+1);

                $sheet->setCellValue($letter1.'6', "Total All Branch");
                $sheet->mergeCells($letter1.'6:'.$letter2.'6');
                $sheet->getStyle($letter1.'6:'.$letter2.'6')->applyFromArray($style_col);

                $sheet->setCellValue($letter1.'7', $bln);
                $sheet->setCellValue($letter2.'7', $bln_prev);
                $sheet->getStyle($letter1.'7:'.$letter2.'7')->applyFromArray($style_col);

                $sheet->getColumnDimension($letter1)->setAutoSize(true);
                $sheet->getColumnDimension($letter2)->setAutoSize(true);
            }
        };

        $draw_header($start_aktiva, "Aktiva");

        $draw_header($start_pasiva, "Pasiva");

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $bc = $data['bid'] == -1 && $rs->fields['kdbid'] == 2 ? self::$ho_jkk : $rs->fields['branch_code'];
                $pnid = $rs->fields['pnid'];
                $coaid = $rs->fields['coaid'];

                if ($rs->fields['coatid'] == 1 && $rs->fields['default_debet'] == 'f')
                {
                    $openingbal = $rs->fields['openingbal'] * -1;
                    $closingbal = $rs->fields['closingbal'] * -1;
                }
                else
                {
                    $openingbal = $rs->fields['openingbal'];
                    $closingbal = $rs->fields['closingbal'];
                }

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['data'][$coaid]['coa'] = $rs->fields['coacode'].' '.$rs->fields['coaname'];

                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $closingbal;

                $data_db[$pnid]['data'][$coaid]['total']['openingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['openingbal'] ?? 0) + $openingbal;
                $data_db[$pnid]['data'][$coaid]['total']['closingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['closingbal'] ?? 0) + $closingbal;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $bc = $data['bid'] == -1 && $rss->fields['kdbid'] == 2 ? self::$ho_jkk : $rss->fields['branch_code'];
                $pnid = $rss->fields['pnid'];
                $coaid = $rss->fields['coaid'];
                $op = $rss->fields['openingbal'];
                $cl = $rss->fields['closingbal'];

                $data_db[$pnid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['total']['openingbal'] = ($data_db[$pnid]['total']['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['total']['closingbal'] = ($data_db[$pnid]['total']['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['data'][$coaid]['coa'] = $rss->fields['coacode'].' '.$rss->fields['coaname'];

                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] = ($data_db[$pnid]['data'][$coaid]['branches'][$bc]['closingbal'] ?? 0) + $cl;

                $data_db[$pnid]['data'][$coaid]['total']['openingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['openingbal'] ?? 0) + $op;
                $data_db[$pnid]['data'][$coaid]['total']['closingbal'] = ($data_db[$pnid]['data'][$coaid]['total']['closingbal'] ?? 0) + $cl;

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            $row_aktiva = 8;
            $row_pasive = 8;
            
            while (!$rs_pos->EOF)
            {
                $pnid = $rs_pos->fields['pnid'];
                $row = $data_db[$pnid] ?? [];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $is_aktiva = ($rs_pos->fields['jenis_pos'] == 1);
                $is_pasiva = ($rs_pos->fields['jenis_pos'] == 2);

                $curr_row = $is_aktiva ? $row_aktiva : ($is_pasiva ? $row_pasive : 0);
                $start_col = $is_aktiva ? $start_aktiva : ($is_pasiva ? $start_pasiva : 0);

                if ($curr_row > 0)
                {
                    $c = $start_col;
                    $col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);

                    $sheet->setCellValue($col_letter.$curr_row, $space.$nama);
                    $c++;

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $amt = floatval($row['branches'][$bc]['closingbal'] ?? 0);
                        $amt_prev = floatval($row['branches'][$bc]['openingbal'] ?? 0);

                        if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                        {
                            $amt = '';
                            $amt_prev = '';
                        }

                        $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                        $sheet->setCellValue($let1.$curr_row, $amt);
                        $sheet->setCellValue($let2.$curr_row, $amt_prev);
                        $c += 2;
                    }

                    if ($has_total)
                    {
                        $amt_tot = floatval($row['total']['closingbal'] ?? 0);
                        $amt_tot_prev = floatval($row['total']['openingbal'] ?? 0);

                        if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                        {
                            $amt_tot = '';
                            $amt_tot_prev = '';
                        }

                        $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                        $sheet->setCellValue($let1.$curr_row, $amt_tot);
                        $sheet->setCellValue($let2.$curr_row, $amt_tot_prev);
                    }

                    $end_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + $cols_per_side - 1);
                    $sheet->getStyle($col_letter.$curr_row.':'.$end_col_letter.$curr_row)->applyFromArray($style_row)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $start_val_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_col + 1);
                        $sheet->getStyle($start_val_letter.$curr_row.':'.$end_col_letter.$curr_row)->getFont()->setUnderline(true);
                        $sheet->getStyle($col_letter.$curr_row.':'.$end_col_letter.$curr_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
                    }

                    $curr_row++;

                    if (isset($row['data']))
                    {
                        foreach ($row['data'] as $coaid => $val)
                        {
                            $space2 = str_repeat("     ", ($rs_pos->fields['level'] + 1));

                            $c_detail = $start_col;
                            $col_let_detail = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_detail);

                            $sheet->setCellValue($col_let_detail.$curr_row, $space2.$val['coa']);
                            $c_detail++;

                            foreach ($data_cabang as $bc => $cabang)
                            {
                                $amt_d = floatval($val['branches'][$bc]['closingbal'] ?? 0);
                                $amt_prev_d = floatval($val['branches'][$bc]['openingbal'] ?? 0);

                                $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_detail);
                                $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_detail+1);

                                $sheet->setCellValue($l1.$curr_row, $amt_d);
                                $sheet->setCellValue($l2.$curr_row, $amt_prev_d);
                                $c_detail += 2;
                            }

                            if ($has_total)
                            {
                                $amt_tot_d = floatval($val['total']['closingbal'] ?? 0);
                                $amt_tot_prev_d = floatval($val['total']['openingbal'] ?? 0);

                                $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_detail);
                                $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_detail+1);

                                $sheet->setCellValue($l1.$curr_row, $amt_tot_d);
                                $sheet->setCellValue($l2.$curr_row, $amt_tot_prev_d);
                            }

                            $curr_row++;
                        }
                    }

                    if ($is_aktiva) $row_aktiva = $curr_row;
                    if ($is_pasiva) $row_pasive = $curr_row;
                }

                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $parent_id = $rs_pos->fields['parent_pnid'];

                    foreach ($data_cabang as $bc => $cabang)
                    {
                        $op = $row['branches'][$bc]['openingbal'] ?? 0;
                        $cl = $row['branches'][$bc]['closingbal'] ?? 0;

                        $data_db[$parent_id]['branches'][$bc]['openingbal'] = ($data_db[$parent_id]['branches'][$bc]['openingbal'] ?? 0) + $op;
                        $data_db[$parent_id]['branches'][$bc]['closingbal'] = ($data_db[$parent_id]['branches'][$bc]['closingbal'] ?? 0) + $cl;
                    }

                    $tot_op = $row['total']['openingbal'] ?? 0;
                    $tot_cl = $row['total']['closingbal'] ?? 0;

                    $data_db[$parent_id]['total']['openingbal'] = ($data_db[$parent_id]['total']['openingbal'] ?? 0) + $tot_op;
                    $data_db[$parent_id]['total']['closingbal'] = ($data_db[$parent_id]['total']['closingbal'] ?? 0) + $tot_cl;
                }

                $rs_pos->MoveNext();
            }
        }

        if (isset($data_db[0]['total']['closingbal']) && $data_db[0]['total']['closingbal'] <> 0)
        {
            $row_aktiva++;

            $c = $start_aktiva;
            $col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);

            $sheet->setCellValue($col_letter.$row_aktiva, 'POS NERACA LAINNYA');
            $c++;

            foreach ($data_cabang as $bc => $cabang)
            {
                $amt = floatval($data_db[0]['branches'][$bc]['closingbal'] ?? 0);
                $amt_prev = floatval($data_db[0]['branches'][$bc]['openingbal'] ?? 0);

                $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($let1.$row_aktiva, $amt);
                $sheet->setCellValue($let2.$row_aktiva, $amt_prev);
                $c += 2;
            }

            if ($has_total)
            {
                $amt_tot = floatval($data_db[0]['total']['closingbal'] ?? 0);
                $amt_tot_prev = floatval($data_db[0]['total']['openingbal'] ?? 0);

                $let1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $let2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c+1);

                $sheet->setCellValue($let1.$row_aktiva, $amt_tot);
                $sheet->setCellValue($let2.$row_aktiva, $amt_tot_prev);
            }

            $end_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_aktiva + $cols_per_side - 1);
            $start_val_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($start_aktiva + 1);

            $sheet->getStyle($col_letter.$row_aktiva.':'.$end_col_letter.$row_aktiva)->applyFromArray($style_row)->getFont()->setBold(true)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
            $sheet->getStyle($start_val_letter.$row_aktiva.':'.$end_col_letter.$row_aktiva)->getFont()->setUnderline(true);
        }

        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->setTitle("Laporan Posisi Keuangan");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Posisi Keuangan Detail.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/
}
?>