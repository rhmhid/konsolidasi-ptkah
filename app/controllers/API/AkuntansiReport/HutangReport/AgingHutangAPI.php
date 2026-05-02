<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as SpreadsheetBorder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AgingHutangAPI extends BaseAPIController
{
    static $kode_kah, $kode_rsjk;

    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/HutangReport/AgingHutangMdl');

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

        $rs = AgingHutangMdl::list($data);

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12)
            $report_month = monthnamelong($data['month']).' '.$data['year'];
        else
            $report_month = $data['month'].'-'.$data['year'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_header = [
            'font'      => [
                'bold'  => true
            ],
            'alignment' => [
                'horizontal'    => Alignment::HORIZONTAL_CENTER,
                'vertical'      => Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle'   => SpreadsheetBorder::BORDER_THIN
                ]
            ],
            'fill'      => [
                'fillType'      => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor'    => [
                    'argb'  => 'FFE9E9E9'
                ]
            ]
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle'   => SpreadsheetBorder::BORDER_THIN
                ]
            ]
        ];

        $sheet->setCellValue('A1', 'LAPORAN AGING HUTANG');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'CABANG : ' . strtoupper($cabang));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2:A2')->getFont()->setBold(true);

        $sheet->setCellValue('A3', 'PERIODE : ' . strtoupper($report_month));
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3:A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Cabang');
        $sheet->setCellValue('C5', 'Saldo');
        $sheet->setCellValue('D5', 'Umur Pembayaran');

        $sheet->setCellValue('D6', '<= 0');
        $sheet->setCellValue('E6', '0 - 30');
        $sheet->setCellValue('F6', '30 - 60');
        $sheet->setCellValue('G6', '60 - 90');
        $sheet->setCellValue('H6', '> 90');

        $sheet->mergeCells('A5:A6');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');
        $sheet->mergeCells('D5:H5');

        $sheet->getStyle('A5:H6')->applyFromArray($style_header);

        $row_pos = 7;
        $no = 1;
        $tot_saldo = $tot_up0 = $tot_up1 = $tot_up2 = $tot_up3 = $tot_up4 = 0;

        while (!$rs->EOF)
        {
            $row = FieldsToObject($rs->fields);

            $saldo = floatval($row->saldo ?? 0);
            $up0   = floatval($row->up0 ?? 0);
            $up1   = floatval($row->up1 ?? 0);
            $up2   = floatval($row->up2 ?? 0);
            $up3   = floatval($row->up3 ?? 0);
            $up4   = floatval($row->up4 ?? 0);

            $tot_saldo += $saldo;
            $tot_up0   += $up0;
            $tot_up1   += $up1;
            $tot_up2   += $up2;
            $tot_up3   += $up3;
            $tot_up4   += $up4;

            $sheet->setCellValue('A'.$row_pos, $no++);
            $sheet->setCellValue('B'.$row_pos, $row->branch_name);
            $sheet->setCellValue('C'.$row_pos, $saldo);
            $sheet->setCellValue('D'.$row_pos, $up0);
            $sheet->setCellValue('E'.$row_pos, $up1);
            $sheet->setCellValue('F'.$row_pos, $up2);
            $sheet->setCellValue('G'.$row_pos, $up3);
            $sheet->setCellValue('H'.$row_pos, $up4);

            $sheet->getStyle('A'.$row_pos.':H'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row_pos.':H'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');

            $row_pos++;
            $rs->MoveNext();
        }

        if ($no > 1)
        {
            $sheet->setCellValue('A'.$row_pos, 'TOTAL');
            $sheet->mergeCells('A'.$row_pos.':B'.$row_pos);

            $sheet->setCellValue('C'.$row_pos, $tot_saldo);
            $sheet->setCellValue('D'.$row_pos, $tot_up0);
            $sheet->setCellValue('E'.$row_pos, $tot_up1);
            $sheet->setCellValue('F'.$row_pos, $tot_up2);
            $sheet->setCellValue('G'.$row_pos, $tot_up3);
            $sheet->setCellValue('H'.$row_pos, $tot_up4);

            $sheet->getStyle('A'.$row_pos.':H'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos.':H'.$row_pos)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('C'.$row_pos.':H'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A'.$row_pos.':H'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9E9E9');
        }
        else
        {
            $sheet->setCellValue('A'.$row_pos, 'Tidak ada data untuk ditampilkan.');
            $sheet->mergeCells('A'.$row_pos.':H'.$row_pos);
            $sheet->getStyle('A'.$row_pos.':H'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'H') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Aging Hutang");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Aging Hutang.xlsx"');
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

        $rs = AgingHutangMdl::detail($data);

        $cabang = $data['bid'] ? Modules::data_cabang_all($data['status_cabang'], $data['bid'])->fields['branch_name'] : 'All';

        if ($data['month'] <= 12)
            $report_month = monthnamelong($data['month']).' '.$data['year'];
        else
            $report_month = $data['month'].'-'.$data['year'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_header = [
            'font'      => [
                'bold'  => true
            ],
            'alignment' => [
                'horizontal'    => Alignment::HORIZONTAL_CENTER,
                'vertical'      => Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle'   => SpreadsheetBorder::BORDER_THIN
                ]
            ],
            'fill'      => [
                'fillType'      => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor'    => [
                    'argb'  => 'FFE9E9E9'
                ]
            ]
        ];

        $style_row = [
            'alignment' => [
                'vertical'  => Alignment::VERTICAL_CENTER
            ],
            'borders'   => [
                'allBorders'    => [
                    'borderStyle'   => SpreadsheetBorder::BORDER_THIN
                ]
            ]
        ];

        // JUDUL LAPORAN (Merge sampai K)
        $sheet->setCellValue('A1', 'LAPORAN AGING HUTANG DETAIL');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

        $sheet->setCellValue('A2', 'CABANG : ' . strtoupper($cabang));
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2:A2')->getFont()->setBold(true);

        $sheet->setCellValue('A3', 'PERIODE : ' . strtoupper($report_month));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3:A3')->getFont()->setBold(true);

        // HEADER TABEL
        $sheet->setCellValue('A5', 'No');
        $sheet->mergeCells('A5:A6');

        $sheet->setCellValue('B5', 'Nama Supplier');
        $sheet->mergeCells('B5:B6');

        $sheet->setCellValue('C5', 'No. Invoice');
        $sheet->mergeCells('C5:C6');

        $sheet->setCellValue('D5', 'Tanggal Invoice');
        $sheet->mergeCells('D5:D6');
        
        $sheet->setCellValue('E5', 'Duedate');
        $sheet->mergeCells('E5:E6');
        
        $sheet->setCellValue('F5', 'Saldo');
        $sheet->mergeCells('F5:F6');

        $sheet->setCellValue('G5', 'Umur Pembayaran');
        $sheet->mergeCells('G5:K5'); // Merge untuk grup umur pembayaran

        // Sub Header Umur Pembayaran
        $sheet->setCellValue('G6', '<= 0');
        $sheet->setCellValue('H6', '0 - 30');
        $sheet->setCellValue('I6', '30 - 60');
        $sheet->setCellValue('J6', '60 - 90');
        $sheet->setCellValue('K6', '> 90');

        $sheet->getStyle('A5:K6')->applyFromArray($style_header);

        $row_pos = 7;
        $no = 1;
        $hasData = false;

        // Variabel Grand Total
        $tot_saldo = $tot_up0 = $tot_up1 = $tot_up2 = $tot_up3 = $tot_up4 = 0;
        
        // Variabel Subtotal Supplier
        $sub_saldo = $sub_up0 = $sub_up1 = $sub_up2 = $sub_up3 = $sub_up4 = 0;
        $current_supplier = null;

        while (!$rs->EOF)
        {
            $hasData = true;
            $row = FieldsToObject($rs->fields);

            // Cek jika ganti supplier
            $is_new_supplier = ($current_supplier !== null && $current_supplier !== $row->nama_supp);

            // 1. CETAK SUBTOTAL JIKA SUPPLIER BERGANTI
            if ($is_new_supplier) {
                $sheet->setCellValue('A'.$row_pos, 'SUBTOTAL ' . strtoupper($current_supplier));
                $sheet->mergeCells('A'.$row_pos.':E'.$row_pos); // Merge No sampai Duedate
                
                $sheet->setCellValue('F'.$row_pos, $sub_saldo);
                $sheet->setCellValue('G'.$row_pos, $sub_up0);
                $sheet->setCellValue('H'.$row_pos, $sub_up1);
                $sheet->setCellValue('I'.$row_pos, $sub_up2);
                $sheet->setCellValue('J'.$row_pos, $sub_up3);
                $sheet->setCellValue('K'.$row_pos, $sub_up4);

                // Styling Baris Subtotal
                $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->applyFromArray($style_row);
                $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('F'.$row_pos.':K'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
                
                // Warna background subtotal agak putih abu-abu
                $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                
                $row_pos++;

                // Reset Subtotal untuk supplier baru
                $sub_saldo = $sub_up0 = $sub_up1 = $sub_up2 = $sub_up3 = $sub_up4 = 0;
            }

            // Set current supplier
            $current_supplier = $row->nama_supp;

            // Parsing Angka
            $saldo = floatval($row->saldo ?? 0);
            $up0   = floatval($row->up0 ?? 0);
            $up1   = floatval($row->up1 ?? 0);
            $up2   = floatval($row->up2 ?? 0);
            $up3   = floatval($row->up3 ?? 0);
            $up4   = floatval($row->up4 ?? 0);

            // Akumulasi Subtotal
            $sub_saldo += $saldo;
            $sub_up0   += $up0;
            $sub_up1   += $up1;
            $sub_up2   += $up2;
            $sub_up3   += $up3;
            $sub_up4   += $up4;

            // Akumulasi Grand Total
            $tot_saldo += $saldo;
            $tot_up0   += $up0;
            $tot_up1   += $up1;
            $tot_up2   += $up2;
            $tot_up3   += $up3;
            $tot_up4   += $up4;

            // 2. CETAK BARIS DATA NORMAL
            $sheet->setCellValue('A'.$row_pos, $no++);
            $sheet->setCellValue('B'.$row_pos, $row->nama_supp);
            $sheet->setCellValue('C'.$row_pos, $row->no_inv);
            
            // Format Tanggal sesuai fungsi di HTML
            $tgl_inv = function_exists('dbtstamp2stringina') ? dbtstamp2stringina($row->apdate) : $row->apdate;
            $tgl_due = function_exists('dbtstamp2stringina') ? dbtstamp2stringina($row->duedate) : $row->duedate;
            
            $sheet->setCellValue('D'.$row_pos, $tgl_inv);
            $sheet->setCellValue('E'.$row_pos, $tgl_due);
            
            $sheet->setCellValue('F'.$row_pos, $saldo);
            $sheet->setCellValue('G'.$row_pos, $up0);
            $sheet->setCellValue('H'.$row_pos, $up1);
            $sheet->setCellValue('I'.$row_pos, $up2);
            $sheet->setCellValue('J'.$row_pos, $up3);
            $sheet->setCellValue('K'.$row_pos, $up4);

            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row_pos.':E'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F'.$row_pos.':K'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');

            $row_pos++;
            $rs->MoveNext();
        }

        if ($hasData)
        {
            // 3. CETAK SUBTOTAL SUPPLIER TERAKHIR
            $sheet->setCellValue('A'.$row_pos, 'SUBTOTAL ' . strtoupper($current_supplier));
            $sheet->mergeCells('A'.$row_pos.':E'.$row_pos);
            
            $sheet->setCellValue('F'.$row_pos, $sub_saldo);
            $sheet->setCellValue('G'.$row_pos, $sub_up0);
            $sheet->setCellValue('H'.$row_pos, $sub_up1);
            $sheet->setCellValue('I'.$row_pos, $sub_up2);
            $sheet->setCellValue('J'.$row_pos, $sub_up3);
            $sheet->setCellValue('K'.$row_pos, $sub_up4);

            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('F'.$row_pos.':K'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
            
            $row_pos++;

            // 4. CETAK GRAND TOTAL
            $sheet->setCellValue('A'.$row_pos, 'GRAND TOTAL');
            $sheet->mergeCells('A'.$row_pos.':E'.$row_pos);
            
            $sheet->setCellValue('F'.$row_pos, $tot_saldo);
            $sheet->setCellValue('G'.$row_pos, $tot_up0);
            $sheet->setCellValue('H'.$row_pos, $tot_up1);
            $sheet->setCellValue('I'.$row_pos, $tot_up2);
            $sheet->setCellValue('J'.$row_pos, $tot_up3);
            $sheet->setCellValue('K'.$row_pos, $tot_up4);

            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFont()->setBold(true);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('F'.$row_pos.':K'.$row_pos)->getNumberFormat()->setFormatCode('#,##0.00');
            
            // Background warna abu agak gelap
            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9E9E9');
        }
        else
        {
            // HANDLE JIKA DATA KOSONG
            $sheet->setCellValue('A'.$row_pos, 'Tidak ada data untuk ditampilkan.');
            $sheet->mergeCells('A'.$row_pos.':K'.$row_pos);
            $sheet->getStyle('A'.$row_pos.':K'.$row_pos)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto Lebar Kolom dari A sampai K
        foreach (range('A', 'K') as $col)
            $sheet->getColumnDimension($col)->setAutoSize(true);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Detail Aging Hutang");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Aging Hutang Detail.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } /*}}}*/
}
?>