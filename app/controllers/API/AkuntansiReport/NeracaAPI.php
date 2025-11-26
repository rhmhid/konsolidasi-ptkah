<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NeracaAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaMdl');
    } /*}}}*/

    public function excel_get ($mytipe) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

        if ($mytipe == 'bs-new') return self::excel_baru($data);
        elseif ($mytipe == 'bs-new-detail') return self::excel_baru_detail($data);

        $data_db = [];
        $tot_asset = $tot_libility = $tot_equity = 0;

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

        $sheet->setCellValue('A1', "Balance Sheet - Monthly");
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', "Accounting Periode");
        $sheet->mergeCells('B2:E2');

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        $sheet->setCellValue('A3', "Report Month");
        $sheet->setCellValue('B3', ": ".$report_month);
        $sheet->mergeCells('B3:E3');

        $sheet->getStyle('A2:E3')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A5', "No.");
        $sheet->setCellValue('B5', "Coacode");
        $sheet->setCellValue('C5', "Coaname");
        $sheet->setCellValue('D5', "Dr / Cr Position");
        $sheet->setCellValue('E5', 'Openning Balance');
        $sheet->setCellValue('F5', 'Closing Balance');

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A5:F5')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:F')->setAutoSize(true);

        $sheet->setCellValue('H5', "No.");
        $sheet->setCellValue('I5', "Coacode");
        $sheet->setCellValue('J5', "Coaname");
        $sheet->setCellValue('K5', "Dr / Cr Position");
        $sheet->setCellValue('L5', 'Openning Balance');
        $sheet->setCellValue('M5', 'Closing Balance');

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('H5:M5')->applyFromArray($style_col);
        $sheet->getColumnDimension('H:M')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $sheet->setCellValue('B2', ": ".$rs_period->UserDate($data['pbegin'], 'M Y').' - '.$rs_period->UserDate($data['pend'], 'M Y'));

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                $tmpdata = array();
                $tmpdata['coacode']         = $rs->fields['coacode'];
                $tmpdata['coaname']         = $rs->fields['coaname'];
                $tmpdata['default_debet']   = $rs->fields['default_debet'];
                $tmpdata['openingbal']      = floatval($rs->fields['openingbal']);
                $tmpdata['closingbal']      = floatval($rs->fields['closingbal']);

                $data_db[$rs->fields['coatid']]['coatype']                      = $rs->fields['coatype'];
                $data_db[$rs->fields['coatid']]['data'][$rs->fields['coaid']]   = $tmpdata;

                if ($rs->fields['coatid'] == 1)
                {
                    // special untuk acc. akumulasi penyusutan, asset tapi default credet
                    if ($rs->fields['default_debet'] == 'f') $tot_asset -= $rs->fields['closingbal'];
                    else $tot_asset += $rs->fields['closingbal'];
                }
                elseif ($rs->fields['coatid'] == 2) $tot_libility += $rs->fields['closingbal'];
                elseif ($rs->fields['coatid'] == 3) $tot_equity += $rs->fields['closingbal'];

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $tmpdata = array();
                $tmpdata['coacode']         = $rss->fields['coacode'];
                $tmpdata['coaname']         = $rss->fields['coaname'];
                $tmpdata['default_debet']   = $rss->fields['default_debet'];
                $tmpdata['openingbal']      = $data_db[3]['data'][$rss->fields['coaid']]['openingbal'] + floatval($rss->fields['openingbal']);
                $tmpdata['closingbal']      = $data_db[3]['data'][$rss->fields['coaid']]['closingbal'] + floatval($rss->fields['closingbal']);

                $data_db[$rss->fields['coatid']]['coatype']                     = $rss->fields['coatype'];
                $data_db[$rss->fields['coatid']]['data'][$rss->fields['coaid']] = $tmpdata;

                $tot_equity += $rss->fields['closingbal'];

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

        $row_aktiva = $row_pasiva = 6;
        foreach ($data_bs as $id => $val)
        {
            $no = 1;

            if ($id < 2)
            {
                if ($id == 1.1 && !empty($data_db[1]['data']))
                {
                    foreach ($data_db[1]['data'] as $key => $tmp)
                    {
                        $posisi = $tmp['default_debet'] == 't' ? 'Dr' : 'Cr';

                        $sheet->setCellValue('A'.$row_aktiva, $no);
                        $sheet->setCellValue('B'.$row_aktiva, $tmp['coacode']);
                        $sheet->setCellValue('C'.$row_aktiva, $tmp['coaname']);
                        $sheet->setCellValue('D'.$row_aktiva, $posisi);
                        $sheet->setCellValue('E'.$row_aktiva, floatval($tmp['openingbal']));
                        $sheet->setCellValue('F'.$row_aktiva, floatval($tmp['closingbal']));

                        $sheet->getStyle('A'.$row_aktiva.':F'.$row_aktiva)->applyFromArray($style_row);

                        $row_aktiva++;
                        $no++;
                    }
                }
                else
                {
                    $sheet->setCellValue('A'.$row_aktiva, '');
                    $sheet->setCellValue('B'.$row_aktiva, $val);

                    if ($id == 1.2)
                        $sheet->setCellValue('F'.$row_aktiva, $tot_asset);

                    $sheet->getStyle('A'.$row_aktiva.':F'.$row_aktiva)->applyFromArray($style_row);
                    $sheet->getStyle('A'.$row_aktiva.':F'.$row_aktiva)->getFont()->setBold(true);

                    $row_aktiva++;
                }
            }
            else
            {
                $mycoatid = substr($id, 0, 1);

                if (($id == 2.1 || $id == 3.1) && !empty($data_db[$mycoatid]['data']))
                {
                    foreach ($data_db[$mycoatid]['data'] as $key => $tmp)
                    {
                        $posisi = $tmp['default_debet'] == 't' ? 'Dr' : 'Cr';

                        $sheet->setCellValue('H'.$row_pasiva, $no);
                        $sheet->setCellValue('I'.$row_pasiva, $tmp['coacode']);
                        $sheet->setCellValue('J'.$row_pasiva, $tmp['coaname']);
                        $sheet->setCellValue('K'.$row_pasiva, $posisi);
                        $sheet->setCellValue('L'.$row_pasiva, floatval($tmp['openingbal']));
                        $sheet->setCellValue('M'.$row_pasiva, floatval($tmp['closingbal']));

                        $sheet->getStyle('H'.$row_pasiva.':M'.$row_pasiva)->applyFromArray($style_row);

                        $row_pasiva++;
                        $no++;
                    }
                }
                else
                {
                    $sheet->setCellValue('H'.$row_pasiva, '');
                    $sheet->setCellValue('I'.$row_pasiva, $val);

                    if ($id == 2.2)
                        $sheet->setCellValue('M'.$row_pasiva, $tot_libility);
                    elseif ($id == 3.2)
                        $sheet->setCellValue('M'.$row_pasiva, $tot_equity);
                    elseif ($id == 3.4)
                        $sheet->setCellValue('M'.$row_pasiva, ($tot_libility + $tot_equity));

                    $sheet->getStyle('H'.$row_pasiva.':M'.$row_pasiva)->applyFromArray($style_row);
                    $sheet->getStyle('H'.$row_pasiva.':M'.$row_pasiva)->getFont()->setBold(true);

                    $row_pasiva++;
                }
            }
        }
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Balance Sheet");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Balance Sheet.xlsx"'); // Set nama file excel nya
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
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

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
        $sheet->mergeCells('A1:G1');

        $sheet->setCellValue('A2', 'LAPORAN POSISI KEUANGAN');
        $sheet->mergeCells('A2:G2');

        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1:G3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:G4');

        $sheet->getStyle('A4:E4')->getFont()->setSize(12);

        $sheet->getStyle('A1:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $bln);
        $sheet->setCellValue('C6', $bln_prev);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:C6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:C')->setAutoSize(true);

        // Buat header tabel nya
        $sheet->setCellValue('E6', "Keterangan");
        $sheet->setCellValue('F6', $bln);
        $sheet->setCellValue('G6', $bln_prev);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('E6:G6')->applyFromArray($style_col);
        $sheet->getColumnDimension('E:G')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                // special untuk acc. akumulasi penyusutan, asset tapi default credet
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

                $data_db[$rs->fields['pnid']]['openingbal'] += $openingbal;
                $data_db[$rs->fields['pnid']]['closingbal'] += $closingbal;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $data_db[$rss->fields['pnid']]['openingbal'] += $rss->fields['openingbal'];
                $data_db[$rss->fields['pnid']]['closingbal'] += $rss->fields['closingbal'];

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            $row_aktiva = $row_pasive = 7;
            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pnid']];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $amount_prev = floatval($row['openingbal']);
                $amount = floatval($row['closingbal']);

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pnid']]['openingbal'] += $amount_prev;
                    $data_db[$rs_pos->fields['parent_pnid']]['closingbal'] += $amount;
                }

                if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_prev = '';
                    $amount = '';
                }

                if ($rs_pos->fields['jenis_pos'] == 1)
                {
                    $sheet->setCellValue('A'.$row_aktiva, $space.$nama);
                    $sheet->setCellValue('B'.$row_aktiva, $amount);
                    $sheet->setCellValue('C'.$row_aktiva, $amount_prev);

                    $sheet->getStyle('A'.$row_aktiva.':C'.$row_aktiva)->applyFromArray($style_row);

                    if ($is_header == 't')
                        $sheet->getStyle('A'.$row_aktiva.':C'.$row_aktiva)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                        $sheet->getStyle('B'.$row_aktiva.':C'.$row_aktiva)->getFont()->setUnderline(true);

                    $row_aktiva++;
                }
                elseif ($rs_pos->fields['jenis_pos'] == 2)
                {
                    $sheet->setCellValue('E'.$row_pasive, $space.$nama);
                    $sheet->setCellValue('F'.$row_pasive, $amount);
                    $sheet->setCellValue('G'.$row_pasive, $amount_prev);

                    $sheet->getStyle('E'.$row_pasive.':G'.$row_pasive)->applyFromArray($style_row);

                    if ($is_header == 't')
                        $sheet->getStyle('E'.$row_pasive.':G'.$row_pasive)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                        $sheet->getStyle('F'.$row_pasive.':G'.$row_pasive)->getFont()->setUnderline(true);

                    $row_pasive++;
                }

                $rs_pos->MoveNext();
            }
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pasive--;

            $sheet->setCellValue('A'.$row_pasive, 'POS NERACA LAINNYA');
            $sheet->setCellValue('B'.$row_pasive, floatval($data_db[0]['closingbal']));
            $sheet->setCellValue('C'.$row_pasive, floatval($data_db[0]['openingbal']));

            $sheet->getStyle('A'.$row_pasive.':C'.$row_pasive)->applyFromArray($style_row);

            $sheet->getStyle('A'.$row_pasive.':C'.$row_pasive)->getFont()->setBold(true);

            $sheet->getStyle('B'.$row_pasive.':C'.$row_pasive)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Posisi Keuangan");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Posisi Keuangan.xlsx"'); // Set nama file excel nya
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
        $empty_aktiva = $empty_pasiva = $without_mapping = true;

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
        $sheet->mergeCells('A1:G1');

        $sheet->setCellValue('A2', 'LAPORAN POSISI KEUANGAN');
        $sheet->mergeCells('A2:G2');

        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.$sdate.' DAN '.$edate);
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1:G3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:G4');

        $sheet->getStyle('A4:E4')->getFont()->setSize(12);

        $sheet->getStyle('A1:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Buat header tabel nya
        $sheet->setCellValue('A6', "Keterangan");
        $sheet->setCellValue('B6', $bln);
        $sheet->setCellValue('C6', $bln_prev);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A6:C6')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:C')->setAutoSize(true);

        // Buat header tabel nya
        $sheet->setCellValue('E6', "Keterangan");
        $sheet->setCellValue('F6', $bln);
        $sheet->setCellValue('G6', $bln_prev);

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('E6:G6')->applyFromArray($style_col);
        $sheet->getColumnDimension('E:G')->setAutoSize(true);

        $rs_period = Modules::get_period_akunting($data);

        if (!$rs_period->EOF)
        {
            $data['paid']   = $rs_period->fields['paid'];
            $data['pbegin'] = $rs_period->fields['pbegin'];
            $data['pend']   = $rs_period->fields['pend'];

            $rs = NeracaMdl::list($data);

            while (!$rs->EOF)
            {
                // special untuk acc. akumulasi penyusutan, asset tapi default credet
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

                $data_db[$rs->fields['pnid']]['openingbal'] += $openingbal;
                $data_db[$rs->fields['pnid']]['closingbal'] += $closingbal;

                $tmpdata = array();
                $tmpdata['coaid']       = $rs->fields['coaid'];
                $tmpdata['coa']         = $rs->fields['coacode'].' '.$rs->fields['coaname'];
                $tmpdata['amount_prev'] = $openingbal;
                $tmpdata['amount']      = $closingbal;

                $data_db[$rs->fields['pnid']]['data'][$rs->fields['coaid']] = $tmpdata;

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);

            while (!$rss->EOF)
            {
                $data_db[$rss->fields['pnid']]['openingbal'] += $rss->fields['openingbal'];
                $data_db[$rss->fields['pnid']]['closingbal'] += $rss->fields['closingbal'];

                $tmpdata = array();
                $tmpdata['coaid']       = $rss->fields['coaid'];
                $tmpdata['coa']         = $rss->fields['coacode'].' '.$rss->fields['coaname'];
                $tmpdata['amount_prev'] = $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']]['amount_prev'] + $rss->fields['openingbal'];
                $tmpdata['amount']      = $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']]['amount'] + $rss->fields['closingbal'];

                $data_db[$rss->fields['pnid']]['data'][$rss->fields['coaid']] = $tmpdata;

                $rss->MoveNext();
            }

            $rs_pos = NeracaMdl::list_pos();

            $row_aktiva = $row_pasive = 7;
            while (!$rs_pos->EOF)
            {
                $row = $data_db[$rs_pos->fields['pnid']];

                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pnid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                $amount_prev = floatval($row['openingbal']);
                $amount = floatval($row['closingbal']);

                // Subtotal Per Header
                if ($rs_pos->fields['parent_pnid'] != '')
                {
                    $data_db[$rs_pos->fields['parent_pnid']]['openingbal'] += $amount_prev;
                    $data_db[$rs_pos->fields['parent_pnid']]['closingbal'] += $amount;
                }

                if ($rs_pos->fields['parent_pnid'] == '' && $rs_pos->fields['sum_total'] == 'f')
                {
                    $amount_prev = '';
                    $amount = '';
                }

                if ($rs_pos->fields['jenis_pos'] == 1)
                {
                    $sheet->setCellValue('A'.$row_aktiva, $space.$nama);
                    $sheet->setCellValue('B'.$row_aktiva, $amount);
                    $sheet->setCellValue('C'.$row_aktiva, $amount_prev);

                    $sheet->getStyle('A'.$row_aktiva.':C'.$row_aktiva)->applyFromArray($style_row)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $sheet->getStyle('B'.$row_aktiva.':C'.$row_aktiva)->getFont()->setUnderline(true);
                        $sheet->getStyle('A'.$row_aktiva.':C'.$row_aktiva)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
                    }

                    $row_aktiva++;

                    if (isset($row['data']))
                    {
                        foreach ($row['data'] as $coaid => $val)
                        {
                            $space2 = str_repeat("     ", ($rs_pos->fields['level'] + 1));

                            $sheet->setCellValue('A'.$row_aktiva, $space2.$val['coa']);
                            $sheet->setCellValue('B'.$row_aktiva, floatval($val['amount']));
                            $sheet->setCellValue('C'.$row_aktiva, floatval($val['amount_prev']));

                            $row_aktiva++;
                        }
                    }
                }
                elseif ($rs_pos->fields['jenis_pos'] == 2)
                {
                    $sheet->setCellValue('E'.$row_pasive, $space.$nama);
                    $sheet->setCellValue('F'.$row_pasive, $amount);
                    $sheet->setCellValue('G'.$row_pasive, $amount_prev);

                    $sheet->getStyle('E'.$row_pasive.':G'.$row_pasive)->applyFromArray($style_row)->getFont()->setBold(true);

                    if ($rs_pos->fields['sum_total'] == 't')
                    {
                        $sheet->getStyle('F'.$row_pasive.':G'.$row_pasive)->getFont()->setUnderline(true);
                        $sheet->getStyle('E'.$row_pasive.':G'.$row_pasive)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');
                    }

                    $row_pasive++;

                    if (isset($row['data']))
                    {
                        foreach ($row['data'] as $coaid => $val)
                        {
                            $space2 = str_repeat("     ", ($rs_pos->fields['level'] + 1));

                            $sheet->setCellValue('E'.$row_pasive, $space2.$val['coa']);
                            $sheet->setCellValue('F'.$row_pasive, floatval($val['amount']));
                            $sheet->setCellValue('G'.$row_pasive, floatval($val['amount_prev']));

                            $row_pasive++;
                        }
                    }
                }

                $rs_pos->MoveNext();
            }
        }

        if ($data_db[0]['closingbal'] <> 0)
        {
            $row_pasive--;

            $sheet->setCellValue('A'.$row_pasive, 'POS NERACA LAINNYA');
            $sheet->setCellValue('B'.$row_pasive, floatval($data_db[0]['closingbal']));
            $sheet->setCellValue('C'.$row_pasive, floatval($data_db[0]['openingbal']));

            $sheet->getStyle('A'.$row_pasive.':C'.$row_pasive)->applyFromArray($style_row)->getFont()->setBold(true)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2ECEC');

            $sheet->getStyle('B'.$row_pasive.':C'.$row_pasive)->getFont()->setUnderline(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Laporan Posisi Keuangan");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Posisi Keuangan.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>