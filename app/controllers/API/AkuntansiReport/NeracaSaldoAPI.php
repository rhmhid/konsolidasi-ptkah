<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NeracaSaldoAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/NeracaSaldoMdl');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

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

        $sheet->setCellValue('A1', "Trial Balance");
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        if ($data['month'] <= 12) $report_month = monthnamelong($data['month']).' '.$data['year'];
        else $report_month = $data['month'].'-'.$data['year'];

        $sheet->setCellValue('A2', "Periode");
        $sheet->setCellValue('B2', ": ".$report_month);
        $sheet->mergeCells('B2:E2');

        $sheet->getStyle('A2:E3')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A4', "No.");
        $sheet->setCellValue('B4', "Coacode");
        $sheet->setCellValue('C4', "Coaname");
        $sheet->setCellValue('D4', "Dr / Cr Position");
        $sheet->setCellValue('E4', "Beginning Balance");
        $sheet->setCellValue('F4', "Debet");
        $sheet->setCellValue('G4', "Credit");
        $sheet->setCellValue('H4', "Ending Balance");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A4:H4')->applyFromArray($style_col);
        $sheet->getColumnDimension('A:H')->setAutoSize(true);

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
                $data_db[$rs->fields['coacode']] = array(
                    'coaid'         => $rs->fields['coaid'],
                    'coaname'       => $rs->fields['coaname'],
                    'default_debet' => $rs->fields['default_debet'],
                    'openingbal'    => floatval($rs->fields['openingbal']),
                    'debet'         => floatval($rs->fields['debet']),
                    'credit'        => floatval($rs->fields['credit']),
                    'closingbal'    => floatval($rs->fields['closingbal']),
                );

                $rs->MoveNext();
            }

            $rss = Modules::laba_rugi($data);
            $coaid_laba_periode_lalu = Modules::$laba_periode_lalu;

            while (!$rss->EOF)
            {
                if ($rss->fields['coaid'] == $coaid_laba_periode_lalu)
                {
                    $data_db[$rss->fields['coacode']] = array(
                        'coaid'         => $rss->fields['coaid'],
                        'coaname'       => $rss->fields['coaname'],
                        'default_debet' => $rss->fields['default_debet'],
                        'openingbal'    => floatval($rss->fields['closingbal']),
                        'debet'         => 0,
                        'credit'        => 0,
                        'closingbal'    => 0,
                    );
                }

                $rss->MoveNext();
            }

            // ORDER BY lagi
            ksort($data_db);

            if (!empty($data_db))
            {
                $no = 1;
                $empty_tb = false;
                $row_idx = 5;
                foreach ($data_db as $coacode => $tmp)
                {
                    $balance = $tmp['default_debet'] == 't' ? ($tmp['debet'] - $tmp['credit']) : ($tmp['credit'] - $tmp['debet']);
                    $balance += $tmp['openingbal'];

                    $sheet->setCellValue('A'.$row_idx, $no);
                    $sheet->setCellValue('B'.$row_idx, $coacode);
                    $sheet->setCellValue('C'.$row_idx, $tmp['coaname']);
                    $sheet->setCellValue('D'.$row_idx, $tmp['default_debet'] == 't' ? 'Dr' : 'Cr');
                    $sheet->setCellValue('E'.$row_idx, floatval($tmp['openingbal']));
                    $sheet->setCellValue('F'.$row_idx, floatval($tmp['debet']));
                    $sheet->setCellValue('G'.$row_idx, floatval($tmp['credit']));
                    $sheet->setCellValue('H'.$row_idx, floatval($balance));

                    // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                    $sheet->getStyle('A'.$row_idx.':H'.$row_idx)->applyFromArray($style_row);

                    $no++;
                    $row_idx++;
                    $tot_deb += $tmp['debet'];
                    $tot_cre += $tmp['credit'];
                }
            }
        }

        $sheet->setCellValue('A'.$row_idx, 'SUBTOTAL');
        $sheet->mergeCells('A'.$row_idx.':D'.$row_idx);

        $sheet->setCellValue('E'.$row_idx, '');
        $sheet->setCellValue('F'.$row_idx, floatval($tot_deb));
        $sheet->setCellValue('G'.$row_idx, floatval($tot_cre));
        $sheet->setCellValue('H'.$row_idx, '');

        // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
        $sheet->getStyle('A'.$row_idx.':H'.$row_idx)->applyFromArray($style_row)->getAlignment()->setHorizontal('right');

        $sheet->getStyle('A'.$row_idx.':H'.$row_idx)->getFont()->setBold(true);
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Neraca Saldo");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Neraca Saldo.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>