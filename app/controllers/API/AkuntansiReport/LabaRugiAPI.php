<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LabaRugiAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/LabaRugiMdl');
    } /*}}}*/

    public function excel_get ($mytipe) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

        if ($mytipe == 'pl-new') return self::excel_baru($data);
        elseif ($mytipe == 'pl-new-daily') return self::excel_baru_daily($data);
        elseif ($mytipe == 'pl-new-detail') return self::excel_baru_detail($data);
        elseif ($mytipe == 'pl-new-detail-daily') return self::excel_baru_detail_daily($data);

        $coacode_last = $subtot_header = [];

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'top'       => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right'     => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left'      => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders'   => [
                'bottom'    => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', "Profit And Loss");
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', "Periode");
        $sheet->setCellValue('B2', ": ".$report_month);
        $sheet->mergeCells('B2:E2');

        $sheet->getStyle('A2:E3')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A4', "No.");
        $sheet->setCellValue('B4', "Coacode");
        $sheet->setCellValue('C4', "Coaname");
        $sheet->setCellValue('D4', "Dr / Cr Position");
        $sheet->setCellValue('E4', $bln);
        $sheet->setCellValue('F4', $bln_prev);
        $sheet->setCellValue('G4', "Until ".$bln);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A4:G4')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:G')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
                $coacode_last[$rs->fields['coatid']] = $rs->fields['coacode'];

                $subtot_header[$rs->fields['coatid']]['headname']           = $rs->fields['coatid'] == 4 ? 'INCOME' : 'COST';
                $subtot_header[$rs->fields['coatid']]['amount_bln_prev']    += $rs->fields['amount_bln_prev'];
                $subtot_header[$rs->fields['coatid']]['amount_bln']         += $rs->fields['amount_bln'];
                $subtot_header[$rs->fields['coatid']]['closingbal']         += $rs->fields['closingbal'];

                $rs->MoveNext();
            }

            $rs->MoveFirst();

            $no = 1;
            $row_idx = 5;
            while (!$rs->EOF)
            {
                $sheet->setCellValue('A'.$row_idx, $no);
                $sheet->setCellValue('B'.$row_idx, $rs->fields['coacode']);
                $sheet->setCellValue('C'.$row_idx, $rs->fields['coaname']);
                $sheet->setCellValue('D'.$row_idx, $rs->fields['default_debet'] == 't' ? 'Dr' : 'Cr');
                $sheet->setCellValue('E'.$row_idx, floatval($rs->fields['amount_bln']));
                $sheet->setCellValue('F'.$row_idx, floatval($rs->fields['amount_bln_prev']));
                $sheet->setCellValue('G'.$row_idx, floatval($rs->fields['closingbal']));

                // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                $sheet->getStyle('A'.$row_idx.':G'.$row_idx)->applyFromArray($style_row);

                $row_idx++;

                if ($coacode_last[$rs->fields['coatid']] == $rs->fields['coacode'])
                {
                    $no = 1;

                    $sheet->setCellValue('A'.$row_idx, 'SUBTOTAL '.$subtot_header[$rs->fields['coatid']]['headname']);
                    $sheet->setCellValue('E'.$row_idx, floatval($subtot_header[$rs->fields['coatid']]['amount_bln']));
                    $sheet->setCellValue('F'.$row_idx, floatval($subtot_header[$rs->fields['coatid']]['amount_bln_prev']));
                    $sheet->setCellValue('G'.$row_idx, floatval($subtot_header[$rs->fields['coatid']]['closingbal']));

                    $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);
                    // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                    $sheet->getStyle('A'.$row_idx.':G'.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');

                    $sheet->getStyle('A'.$row_idx.':G'.$row_idx)->getFont()->setBold(true);

                    $row_idx++;

                    $sheet->mergeCells('A'.$row_idx.':G'.$row_idx);

                    $row_idx++;
                }
                else
                    $no++;

                if ($rs->fields['coatid'] == 4)
                {
                    $balance_income_prev += $rs->fields['amount_bln_prev'];
                    $balance_income_bln += $rs->fields['amount_bln'];
                    $balance_income += $rs->fields['closingbal'];
                }

                if ($rs->fields['coatid'] != 4)
                {
                    $balance_cost_prev += $rs->fields['amount_bln_prev'];
                    $balance_cost_bln += $rs->fields['amount_bln'];
                    $balance_cost += $rs->fields['closingbal'];
                }

                $rs->MoveNext();
            }
        }

        $tot_pnl_bln_prev = $balance_income_prev - $balance_cost_prev;
        $tot_pnl_bln = $balance_income_bln - $balance_cost_bln;
        $tot_pnl_thn = $balance_income - $balance_cost;

        $sheet->setCellValue('A'.$row_idx, 'TOTAL LABA / RUGI');
        $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);

        $sheet->setCellValue('E'.$row_idx, floatval($tot_pnl_bln));
        $sheet->setCellValue('F'.$row_idx, floatval($tot_pnl_bln_prev));
        $sheet->setCellValue('G'.$row_idx, floatval($tot_pnl_thn));

        // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
        $sheet->getStyle('A'.$row_idx.':G'.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');

        $sheet->getStyle('A'.$row_idx.':G'.$row_idx)->getFont()->setBold(true);
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Profit And Loss");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Profit And Loss.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function excel_baru ($data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

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

        $rs_pos = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:D1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA');
        $sheet->mergeCells('A2:D2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A1:D3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:D4');

        $sheet->getStyle('A4:D4')->getFont()->setSize(12);

        $sheet->getStyle('A1:D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $bln);
        $sheet->setCellValue('C6', $bln_prev);
        $sheet->setCellValue('D6', "Until ".$bln);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:D6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:D')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
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

                $data_db[$rs->fields['pplrid']]['amount_bln_prev'] += floatval($amount_bln_prev);
                $data_db[$rs->fields['pplrid']]['amount_bln'] += floatval($amount_bln);
                $data_db[$rs->fields['pplrid']]['closingbal'] += floatval($closingbal);

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos_rekap();

            $row_pos = 7;
            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pplrid']];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $amount_bln_prev = $row['amount_bln_prev'];
                $amount_bln = $row['amount_bln'];
                $closingbal = $row['closingbal'];

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pplrid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pplrid']]['amount_bln_prev'] += $row['amount_bln_prev'];
                    $data_db[$rs_pos->fields['parent_pplrid']]['amount_bln'] += $row['amount_bln'];
                    $data_db[$rs_pos->fields['parent_pplrid']]['closingbal'] += $row['closingbal'];
                }

                if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_bln_prev = '';
                    $amount_bln = '';
                    $closingbal = '';
                }

                $sheet->setCellValue('A'.$row_pos, $space.$nama);
                $sheet->setCellValue('B'.$row_pos, $amount_bln);
                $sheet->setCellValue('C'.$row_pos, $amount_bln_prev);
                $sheet->setCellValue('D'.$row_pos, $closingbal);

                $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->applyFromArray($style_row);

                if ($is_header == 't')
                    $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->getFont()->setBold(true);

                if ($rs_pos->fields['sum_total'] == 't')
                    $sheet->getStyle('B'.$row_pos.':D'.$row_pos)->getFont()->setUnderline(true);

                $row_pos++;

                $rs_pos->MoveNext();
            }
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pos++;

            $sheet->setCellValue('A'.$row_pos, 'POS LABA/RUGI LAINNYA');
            $sheet->setCellValue('B'.$row_pos, floatval($data_db[0]['amount_bln']));
            $sheet->setCellValue('C'.$row_pos, floatval($data_db[0]['amount_bln_prev']));
            $sheet->setCellValue('D'.$row_pos, floatval($data_db[0]['closingbal']));

            $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->applyFromArray($style_row);

            $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->getFont()->setBold(true);

            $sheet->getStyle('B'.$row_pos.':D'.$row_pos)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Rekap Rugi - Laba");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Rekap Rugi - Laba.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function excel_baru_detail ($data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

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

        $rs_pos = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:D1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA');
        $sheet->mergeCells('A2:D2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A1:D3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:D4');

        $sheet->getStyle('A4:D4')->getFont()->setSize(12);

        $sheet->getStyle('A1:D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $bln);
        $sheet->setCellValue('C6', $bln_prev);
        $sheet->setCellValue('D6', "Until ".$bln);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:D6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:D')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = LabaRugiMdl::list($data);

            while (!$rs->EOF)
            {
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

                $data_db[$rs->fields['pplid']]['amount_prev'] += floatval($amount_prev);
                $data_db[$rs->fields['pplid']]['amount'] += floatval($amount);
                $data_db[$rs->fields['pplid']]['closingbal'] += floatval($closingbal);

                $rs->MoveNext();
            }

            $rs_pos = LabaRugiMdl::list_pos();

            $row_pos = 7;
            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pplid']];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $amount_prev = floatval($row['amount_prev']);
                $amount = floatval($row['amount']);
                $closingbal = floatval($row['closingbal']);

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pplid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pplid']]['amount_prev'] += $amount_prev;
                    $data_db[$rs_pos->fields['parent_pplid']]['amount'] += $amount;
                    $data_db[$rs_pos->fields['parent_pplid']]['closingbal'] += $closingbal;
                }

                if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_bln_prev = '';
                    $amount_bln = '';
                    $closingbal = '';
                }

                $sheet->setCellValue('A'.$row_pos, $space.$nama);
                $sheet->setCellValue('B'.$row_pos, $amount);
                $sheet->setCellValue('C'.$row_pos, $amount_prev);
                $sheet->setCellValue('D'.$row_pos, $closingbal);

                $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->applyFromArray($style_row);

                if ($is_header == 't')
                    $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->getFont()->setBold(true);

                if ($rs_pos->fields['sum_total'] == 't')
                {
                    $sheet->getStyle('B'.$row_pos.':D'.$row_pos)->getFont()->setUnderline(true);
                    $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
                }

                $row_pos++;

                $rs_pos->MoveNext();
            }
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pos++;

            $sheet->setCellValue('A'.$row_pos, 'POS NERACA LAINNYA');
            $sheet->setCellValue('B'.$row_pos, floatval($data_db[0]['amount']));
            $sheet->setCellValue('C'.$row_pos, floatval($data_db[0]['amount_prev']));
            $sheet->setCellValue('D'.$row_pos, floatval($data_db[0]['closingbal']));

            $sheet->getStyle('A'.$row_pos.':D'.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');

            $sheet->getStyle('B'.$row_pos.':D'.$row_pos)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Detail Rugi - Laba");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Detail Rugi - Laba.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function excel_baru_daily ($data) /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);
        $rs_pos = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:C1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA DAILY');
        $sheet->mergeCells('A2:C2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:C3');

        $sheet->getStyle('A1:C3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:C4');

        $sheet->getStyle('A4:C4')->getFont()->setSize(12);

        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $sdate." sd ".$edate);
        $sheet->setCellValue('C6', "Until ".$edate);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:C6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:C')->setAutoSize(true);

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            /*if ($rs->fields['coatid'] == 5)
            {
                $amount_period = $rs->fields['amount_period'] * -1;
                $amount_untill = $rs->fields['amount_untill'] * -1;
            }
            else
            {*/
                $amount_period = $rs->fields['amount_period'];
                $amount_untill = $rs->fields['amount_untill'];
            // }

            $data_db[$rs->fields['pplrid']]['amount_period'] += $amount_period;
            $data_db[$rs->fields['pplrid']]['amount_untill'] += $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos_rekap();

        $row_pos = 7;
        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pplrid']];

            $space = str_repeat("     ", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplrid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            $amount_period = format_uang($row['amount_period'], 2);
            $amount_untill = format_uang($row['amount_untill'], 2);

            // Subtotal Per Header
            if ($rs_pos->fields['parent_pplrid'] != '')
            {
                $data_db[$rs_pos->fields['parent_pplrid']]['amount_period'] += $row['amount_period'];
                $data_db[$rs_pos->fields['parent_pplrid']]['amount_untill'] += $row['amount_untill'];
            }

            if ($rs_pos->fields['parent_pplrid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $amount_period = '';
                $amount_untill = '';
            }

            $sheet->setCellValue('A'.$row_pos, $space.$nama);
            $sheet->setCellValue('B'.$row_pos, $amount_period);
            $sheet->setCellValue('C'.$row_pos, $amount_untill);

            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->applyFromArray($style_row);

            if ($is_header == 't')
                $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFont()->setBold(true);

            if ($rs_pos->fields['sum_total'] == 't')
                $sheet->getStyle('B'.$row_pos.':C'.$row_pos)->getFont()->setUnderline(true);

            $row_pos++;

            $rs_pos->MoveNext();
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pos++;

            $sheet->setCellValue('A'.$row_pos, 'POS LABA/RUGI LAINNYA');
            $sheet->setCellValue('B'.$row_pos, floatval($data_db[0]['amount_period']));
            $sheet->setCellValue('C'.$row_pos, floatval($data_db[0]['amount_untill']));

            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->applyFromArray($style_row);

            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFont()->setBold(true);

            $sheet->getStyle('B'.$row_pos.':C'.$row_pos)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Rekap Rugi - Laba Daily");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Rekap Rugi - Laba Daily.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/

    public function excel_baru_detail_daily ($data) /*{{{*/
    {
        $data = array(
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
        $data['edate'] = date('Y-m-d', strtotime($data['edate']));
        $data['pmonth'] = date('Y', strtotime($data['sdate'])).'-01-01';

        $tgl_cetak = date('Y-m-d');
        $sdate = dbtstamp2stringina($data['sdate']);
        $edate = dbtstamp2stringina($data['edate']);

        $rs_pos = $data_pos = [];
        $empty_pos = $without_mapping = true;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
        ];

        $sheet->setCellValue('A1', dataConfigs('company_name'));
        $sheet->mergeCells('A1:C1');

        $sheet->setCellValue('A2', 'LAPORAN REKAP RUGI / LABA DAILY');
        $sheet->mergeCells('A2:C2');

        $sheet->setCellValue('A3', 'PER '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:C3');

        $sheet->getStyle('A1:C3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:C4');

        $sheet->getStyle('A4:C4')->getFont()->setSize(12);

        $sheet->getStyle('A1:C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $sdate." sd ".$edate);
        $sheet->setCellValue('C6', "Until ".$edate);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:C6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:C')->setAutoSize(true);

        $rs = LabaRugiMdl::list_daily($data);

        while (!$rs->EOF)
        {
            /*if ($rs->fields['coatid'] == 5)
            {
                $amount_period = $rs->fields['amount_period'] * -1;
                $amount_untill = $rs->fields['amount_untill'] * -1;
            }
            else
            {*/
                $amount_period = $rs->fields['amount_period'];
                $amount_untill = $rs->fields['amount_untill'];
            // }

            $data_db[$rs->fields['pplid']]['amount_period'] += $amount_period;
            $data_db[$rs->fields['pplid']]['amount_untill'] += $amount_untill;

            $rs->MoveNext();
        }

        $rs_pos = LabaRugiMdl::list_pos();

        $row_pos = 7;
        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pplid']];

            $space = str_repeat("     ", $rs_pos->fields['level']);
            $is_header = $rs_pos->fields['parent_pplid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
            $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

            $amount_period = floatval($row['amount_period']);
            $amount_untill = floatval($row['amount_untill']);

            // Subtotal Per Header
            if ($rs_pos->fields['parent_pplid'] != '')
            {
                $data_db[$rs_pos->fields['parent_pplid']]['amount_period'] += $amount_period;
                $data_db[$rs_pos->fields['parent_pplid']]['amount_untill'] += $amount_untill;
            }

            if ($rs_pos->fields['parent_pplid'] == '' && $rs_pos->fields['sum_total'] == 'f')
            {
                $amount_period = '';
                $amount_untill = '';
            }

            $sheet->setCellValue('A'.$row_pos, $space.$nama);
            $sheet->setCellValue('B'.$row_pos, $amount_period);
            $sheet->setCellValue('C'.$row_pos, $amount_untill);

            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->applyFromArray($style_row);

            if ($is_header == 't')
                $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFont()->setBold(true);

            if ($rs_pos->fields['sum_total'] == 't')
            {
                $sheet->getStyle('B'.$row_pos.':C'.$row_pos)->getFont()->setUnderline(true);
                $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
            }

            $row_pos++;

            $rs_pos->MoveNext();
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pos++;

            $sheet->setCellValue('A'.$row_pos, 'POS NERACA LAINNYA');
            $sheet->setCellValue('B'.$row_pos, floatval($data_db[0]['amount_period']));
            $sheet->setCellValue('C'.$row_pos, floatval($data_db[0]['amount_untill']));

            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->applyFromArray($style_row)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos.':C'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');

            $sheet->getStyle('B'.$row_pos.':C'.$row_pos)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Detail PnL Daily");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Detail Rugi - Laba Daily.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>