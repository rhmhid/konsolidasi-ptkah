<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ArusKasAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/ArusKasMdl');
    } /*}}}*/

    public function excel_get ($mytipe) /*{{{*/
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
        );

        if ($mytipe == 'cf-indirect') return self::excel_indirect($mytipe, $data);
        else return self::excel_direct($mytipe, $data);
    } /*}}}*/

    public function excel_direct($mytipe, $data)
    {
        $data = array(
            'bid'           => get_var('bid'),
            'status_cabang' => get_var('status_cabang'),
            'status_coa'    => get_var('status_coa'),
            'month'         => intval(get_var('month')),
            'year'          => get_var('year'),
            'sdate'         => get_var('sdate', date('d-m-Y')),
            'edate'         => get_var('edate', date('d-m-Y')),
        );

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_db = $data_mapping = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $amount_cf = [
            'branches'  => [],
            'total'     => 0
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rs_cabang = Modules::data_cabang_all($data['status_cabang'], $data['bid'], 'f');

        $data_cabang = [];
        while (!$rs_cabang->EOF)
        {
            $data_cabang[$rs_cabang->fields['branch_code']] = $rs_cabang->fields;

            $rs_cabang->MoveNext();
        }

        $num_cabang = count($data_cabang);
        $has_total  = $num_cabang > 1;

        $total_cols = 1 + ($num_cabang * 3) + ($has_total ? 3 : 0);
        $last_col_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($total_cols);

        $style_col = [
            'font'      => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ];

        $subtitle = $mytipe == 'cf-direct-daily' ? ' DAILY' : '';

        if ($mytipe == 'cf-direct-daily')
            $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' sd '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));
        else
        {
            if ($data['month'] <= 12)
                $periode = monthnamelong($data['month']).' '.$data['year'];
            else
                $periode = $data['month'].'-'.$data['year'];
        }

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:'.$last_col_letter.'1');
        $sheet->setCellValue('A2', 'LAPORAN ARUS KAS ( METODE DIRECT )'.$subtitle);
        $sheet->mergeCells('A2:'.$last_col_letter.'2');
        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.strtoupper($periode));
        $sheet->mergeCells('A3:'.$last_col_letter.'3');
        $sheet->getStyle('A1:'.$last_col_letter.'3')->getFont()->setBold(true)->setSize(16);
        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:'.$last_col_letter.'4');
        $sheet->getStyle('A4:'.$last_col_letter.'4')->getFont()->setSize(12);
        $sheet->getStyle('A1:'.$last_col_letter.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('A5', 'POS ARUS KAS');
        $sheet->getStyle('A5')->applyFromArray($style_col);

        $c_idx = 2;
        foreach ($data_cabang as $bc => $cab)
        {
            $l_start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx);
            $l_end   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l_start.'5', strtoupper($cab['branch_name']));
            $sheet->mergeCells($l_start.'5:'.$l_end.'5');
            $sheet->getStyle($l_start.'5:'.$l_end.'5')->applyFromArray($style_col);
            $c_idx += 3;
        }

        if ($has_total)
        {
            $l_start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx);
            $l_end   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l_start.'5', 'TOTAL ALL BRANCH');
            $sheet->mergeCells($l_start.'5:'.$l_end.'5');
            $sheet->getStyle($l_start.'5:'.$l_end.'5')->applyFromArray($style_col);
        }

        $rs = ArusKasMdl::list($mytipe, $data);

        while (!$rs->EOF)
        {
            $bc = $rs->fields['branch_code'] ?? '';
            $amt = floatval($rs->fields['amount']);
            $id1 = $rs->fields['pcfid'];
            $id2 = $rs->fields['parent_pcfid'];
            $id3 = $rs->fields['pcfid_parent'];

            $data_db[$id1]['branches'][$bc] = ($data_db[$id1]['branches'][$bc] ?? 0) + $amt;
            $data_db[$id1]['total']         = ($data_db[$id1]['total'] ?? 0) + $amt;

            if ($id2 != '')
            {
                $data_db[$id2]['branches'][$bc] = ($data_db[$id2]['branches'][$bc] ?? 0) + $amt;
                $data_db[$id2]['total']         = ($data_db[$id2]['total'] ?? 0) + $amt;
            }

            if ($id3 != '')
            {
                $data_db[$id3]['branches'][$bc] = ($data_db[$id3]['branches'][$bc] ?? 0) + $amt;
                $data_db[$id3]['total']         = ($data_db[$id3]['total'] ?? 0) + $amt;
            }

            $rs->MoveNext();
        }

        $rs_pos = ArusKasMdl::list_pos(1);

        $row_cf = 6;
        while (!$rs_pos->EOF)
        {
            $pcfid = $rs_pos->fields['pcfid'];
            $has_value = false;
            $tot_amt = floatval($data_db[$pcfid]['total'] ?? 0);

            if (abs($tot_amt) > 0.001) $has_value = true;

            if (isset($data_db[$pcfid]['branches']))
            {
                foreach ($data_db[$pcfid]['branches'] as $v)
                    if (abs(floatval($v)) > 0.001) $has_value = true;
            }

            if ($has_value || $rs_pos->fields['parent_pcfid'] == '')
            {
                $level = $rs_pos->fields['level'];
                $space = str_repeat("     ", $level);
                $is_header = $rs_pos->fields['parent_pcfid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $sheet->setCellValue('A'.$row_cf, $space.$nama);
                $c_idx = 2; 

                $render_cols = function ($amt) use ($sheet, &$c_idx, $row_cf, $level)
                {
                    $l1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx++);
                    $l2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx++);
                    $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx++);

                    if ($level == 0) $sheet->setCellValue($l3.$row_cf, $amt <> 0 ? floatval($amt) : "");
                    elseif ($level == 1) $sheet->setCellValue($l2.$row_cf, $amt <> 0 ? floatval($amt) : "");
                    else $sheet->setCellValue($l1.$row_cf, $amt <> 0 ? floatval($amt) : "");
                };

                foreach ($data_cabang as $bc => $cab)
                {
                    $amt = $data_db[$pcfid]['branches'][$bc] ?? 0;
                    $render_cols($amt);

                    if ($level == 0) $amount_cf['branches'][$bc] = ($amount_cf['branches'][$bc] ?? 0) + $amt;
                }

                if ($has_total)
                {
                    $render_cols($tot_amt);

                    if ($level == 0) $amount_cf['total'] += $tot_amt;
                }

                $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->applyFromArray($style_row);

                if ($is_header == 't' && trim($rs_pos->fields['nama_pos']) != '')
                {
                    $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
                }

                $row_cf++;
            }

            $rs_pos->MoveNext();
        }

        $sheet->setCellValue('A'.$row_cf, 'Net Increase and Decrease in Cash and Cash Equivalents');
        $c_idx = 2;

        foreach ($data_cabang as $bc => $cab)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($amount_cf['branches'][$bc] ?? 0));
            $c_idx += 3;
        }

        if ($has_total)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($amount_cf['total'] ?? 0));
        }

        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

        $rs_awal = ArusKasMdl::direct_saldo($mytipe, $data);
        $cf_awal = [
            'branches'  => [],
            'total'     => [
                'bamount' => 0,
                'eamount' => 0
            ]
        ];

        while (!$rs_awal->EOF)
        {
            $bc = $rs_awal->fields['branch_code'] ?? '';
            $bamt = floatval($rs_awal->fields['bamount']);
            $eamt = floatval($rs_awal->fields['eamount']);

            $cf_awal['branches'][$bc]['bamount'] = ($cf_awal['branches'][$bc]['bamount'] ?? 0) + $bamt;
            $cf_awal['branches'][$bc]['eamount'] = ($cf_awal['branches'][$bc]['eamount'] ?? 0) + $eamt;
            $cf_awal['total']['bamount'] += $bamt;
            $cf_awal['total']['eamount'] += $eamt;

            $rs_awal->MoveNext();
        }

        $row_cf++;
        $sheet->setCellValue('A'.$row_cf, 'Cash and Cash Equivalents at Beginning of Period');
        $c_idx = 2;
        foreach ($data_cabang as $bc => $cab)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($cf_awal['branches'][$bc]['bamount'] ?? 0));
            $c_idx += 3;
        }

        if ($has_total)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($cf_awal['total']['bamount'] ?? 0));
        }

        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

        $row_cf++;
        $sheet->setCellValue('A'.$row_cf, 'Cash and Cash Equivalents at End of Period');
        $c_idx = 2;
        foreach ($data_cabang as $bc => $cab)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($cf_awal['branches'][$bc]['eamount'] ?? 0));
            $c_idx += 3;
        }

        if ($has_total)
        {
            $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
            $sheet->setCellValue($l3.$row_cf, floatval($cf_awal['total']['eamount'] ?? 0));
        }

        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $has_lainnya = false;

        if (abs(floatval($data_db[0]['total'] ?? 0)) > 0.001) $has_lainnya = true;

        if (isset($data_db[0]['branches']))
            foreach ($data_db[0]['branches'] as $v) if (abs(floatval($v)) > 0.001) $has_lainnya = true;

        if ($has_lainnya)
        {
            $row_cf++;
            $sheet->setCellValue('A'.$row_cf, '');
            $sheet->mergeCells('A'.$row_cf.':'.$last_col_letter.$row_cf);

            $row_cf++;
            $sheet->setCellValue('A'.$row_cf, 'POS ARUS KAS LAINNYA');
            $c_idx = 2;
            foreach ($data_cabang as $bc => $cab)
            {
                $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
                $sheet->setCellValue($l3.$row_cf, floatval($data_db[0]['branches'][$bc] ?? 0));
                $c_idx += 3;
            }

            if ($has_total)
            {
                $l3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c_idx + 2);
                $sheet->setCellValue($l3.$row_cf, floatval($data_db[0]['total'] ?? 0));
            }

            $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_cf.':'.$last_col_letter.$row_cf)->getFont()->setBold(true);
        }

        $sheet->getStyle('B6:'.$last_col_letter.$row_cf)->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Arus Kas - Direct".$subtitle);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Arus Kas - Direct'.$subtitle.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
?>