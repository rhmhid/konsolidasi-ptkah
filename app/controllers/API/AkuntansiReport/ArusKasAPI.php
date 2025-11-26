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
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
        );

        if ($mytipe == 'cf-indirect') return self::excel_indirect($mytipe, $data);
        else return self::excel_direct($mytipe, $data);
    } /*}}}*/

    public function excel_direct ($mytipe, $data) /*{{{*/
    {
        $data = array(
            'month' => intval(get_var('month')),
            'year'  => get_var('year'),
            'sdate' => get_var('sdate', date('d-m-Y')),
            'edate' => get_var('edate', date('d-m-Y')),
        );

        $tgl_cetak = date('Y-m-d');
        $rs_pos = $data_db = $data_mapping = $data_pos = [];
        $empty_pos = $without_mapping = true;
        $amount_cf = 0;

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

        if ($mytipe == 'cf-direct-daily') $subtitle = ' DAILY';

        $sheet->setCellValue('A2', 'LAPORAN ARUS KAS ( METODE DIRECT )'.$subtitle);
        $sheet->mergeCells('A2:D2');

        if ($mytipe == 'cf-direct-daily')
            $periode = dbtstamp2stringina(date('Y-m-d', strtotime($data['sdate']))).' sd '.dbtstamp2stringina(date('Y-m-d', strtotime($data['edate'])));
        else
        {
            if ($data['month'] <= 12)
                $periode = monthnamelong($data['month']).' '.$data['year'];
            else
                $periode = $data['month'].'-'.$data['year'];
        }

        $sheet->setCellValue('A3', 'UNTUK PERIODE YANG BERAKHIR '.strtoupper($periode));
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A1:D3')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A4', strtoupper('Tgl cetak : '.dbtstamp2stringina($tgl_cetak)));
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A4:D4')->getFont()->setSize(12);

        $sheet->getStyle('A1:D4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $rs = ArusKasMdl::list($mytipe, $data);

        while (!$rs->EOF)
        {
            $data_db[$rs->fields['pcfid']]['amount'] += $rs->fields['amount'];
            $data_db[$rs->fields['parent_pcfid']]['amount'] += $rs->fields['amount'];
            $data_db[$rs->fields['pcfid_parent']]['amount'] += $rs->fields['amount'];

            $rs->MoveNext();
        }

        $rs_pos = ArusKasMdl::list_pos(1);

        $row_cf = 6;
        while (!$rs_pos->EOF)
        {
            $row = $data_db[$rs_pos->fields['pcfid']];
            $amount = floatval($row['amount']);

            if ($amount <> 0)
            {
                $space = str_repeat("     ", $rs_pos->fields['level']);
                $is_header = $rs_pos->fields['parent_pcfid'] == '' || $rs_pos->fields['sum_total'] == 't' ? 't' : 'f';
                $nama = $rs_pos->fields['kode_pos'].' '.$rs_pos->fields['nama_pos'];

                if ($rs_pos->fields['level'] == 0)
                {
                    $amount_detail = "";
                    $amount_subheader = "";
                    $amount_header = floatval($amount);

                    $amount_cf += $amount;
                }
                elseif ($rs_pos->fields['level'] == 1)
                {
                    $amount_detail = "";
                    $amount_subheader = floatval($amount);
                    $amount_header = "";
                }
                else
                {
                    $amount_detail = floatval($amount);
                    $amount_subheader = "";
                    $amount_header = "";
                }

                $sheet->setCellValue('A'.$row_cf, $space.$nama);
                $sheet->setCellValue('B'.$row_cf, $amount_detail);
                $sheet->setCellValue('C'.$row_cf, $amount_subheader);
                $sheet->setCellValue('D'.$row_cf, $amount_header);

                $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->applyFromArray($style_row);

                if ($is_header == 't' && trim($rs_pos->fields['nama_pos']) != '')
                {
                    $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
                }

                $row_cf++;
            }

            $rs_pos->MoveNext();
        }

        $sheet->setCellValue('A'.$row_cf, 'Net Incerease and Decrease in Cash and Cash Equivalents');
        $sheet->setCellValue('B'.$row_cf, '');
        $sheet->setCellValue('C'.$row_cf, '');
        $sheet->setCellValue('D'.$row_cf, floatval($amount_cf));

        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

        $rs_awal = ArusKasMdl::direct_saldo($mytipe, $data);

        $cf_speriod = $cf_eperiod = 0;
        while (!$rs_awal->EOF)
        {
            $cf_speriod += $rs_awal->fields['bamount'];
            $cf_eperiod += $rs_awal->fields['eamount'];

            $rs_awal->MoveNext();
        }

        $row_cf++;
        $sheet->setCellValue('A'.$row_cf, 'Cash and Cash Equivalents at Beginning of Period');
        $sheet->setCellValue('B'.$row_cf, '');
        $sheet->setCellValue('C'.$row_cf, '');
        $sheet->setCellValue('D'.$row_cf, floatval($cf_speriod));

        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

        $row_cf++;
        $sheet->setCellValue('A'.$row_cf, 'Cash and Cash Equivalents at End of Period');
        $sheet->setCellValue('B'.$row_cf, '');
        $sheet->setCellValue('C'.$row_cf, '');
        $sheet->setCellValue('D'.$row_cf, floatval($cf_eperiod));

        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFont()->setBold(true);
        $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

        if ($data_db[0]['amount'] <> 0)
        {
            $row_cf++;
            $sheet->setCellValue('A'.$row_cf, '');
            $sheet->mergeCells('A'.$row_cf.':D'.$row_cf);

            $row_cf++;
            $sheet->setCellValue('A'.$row_cf, 'POS ARUS KAS LAINNYA');
            $sheet->setCellValue('B'.$row_cf, '');
            $sheet->setCellValue('C'.$row_cf, '');
            $sheet->setCellValue('D'.$row_cf, floatval($data_db[0]['amount']));

            $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->applyFromArray($style_row);
            $sheet->getStyle('A'.$row_cf.':D'.$row_cf)->getFont()->setBold(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Arus Kas - Direct".$subtitle);

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Arus Kas - Direct'.$subtitle.'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>