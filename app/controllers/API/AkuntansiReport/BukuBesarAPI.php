<?php if (!defined('BASEPATH')) exit("No direct script access allowed");

require_once APPPATH . '/libraries/BaseAPIController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class BukuBesarAPI extends BaseAPIController
{
    public function __construct () /*{{{*/
    {
        parent::__construct();

        $this->load->model('AkuntansiReport/BukuBesarMdl');
    } /*}}}*/

    public function excel_get () /*{{{*/
    {
        $data = array(
            'sdate'         => get_var('sdate'),
            'edate'         => get_var('edate'),
            'jtid'          => get_var('jtid'),
            'is_posted'     => get_var('is_posted'),
            'coaid_from'    => get_var('coaid_from'),
            'coaid_to'      => get_var('coaid_to'),
            'pccid'         => get_var('pccid'),
            'with_bb'       => get_var('with_bb'),
        );

        $coa_vs = get_var('coa_vs');

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

        $huruf_header = $coa_vs == 't' ? "O" : "N";

        $sheet->setCellValue('A1', "Overview Ledger");
        $sheet->mergeCells('A1:'.$huruf_header.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', "Periode");
        $sheet->setCellValue('B2', ": ".$data['sdate'].' UNTIL '.$data['edate']);
        $sheet->mergeCells('B2:'.$huruf_header.'2');

        $sheet->getStyle('A2:'.$huruf_header.'3')->getFont()->setBold(true)->setSize(12);

        // Buat header tabel nya
        $sheet->setCellValue('A5', "Tgl Trans");
        $sheet->setCellValue('B5', "GL Trans");
        $sheet->setCellValue('C5', "Account Short Tex");
        $sheet->setCellValue('D5', "Description");

        if ($coa_vs == 't')
        {
            $sheet->setCellValue('E5', "C.O.A Lawan");
            $sheet->setCellValue('F5', "User Entry");
            $sheet->setCellValue('G5', "Supplier");
            $sheet->setCellValue('H5', "Debet");
            $sheet->setCellValue('I5', "Credit");
            $sheet->setCellValue('J5', "Balance");
            $sheet->setCellValue('K5', "Doc Number");
            $sheet->setCellValue('L5', "Reff Code");
            $sheet->setCellValue('M5', "Transaction Type");
            $sheet->setCellValue('N5', "Short Text");
            $sheet->setCellValue('O5', "Cost Center");

            // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $sheet->getStyle('A5:O5')->applyFromArray($style_col);
            $sheet->getColumnDimension('A:O')->setAutoSize(true);
        }
        else
        {
            $sheet->setCellValue('E5', "User Entry");
            $sheet->setCellValue('F5', "Supplier");
            $sheet->setCellValue('G5', "Debet");
            $sheet->setCellValue('H5', "Credit");
            $sheet->setCellValue('I5', "Balance");
            $sheet->setCellValue('J5', "Doc Number");
            $sheet->setCellValue('K5', "Reff Code");
            $sheet->setCellValue('L5', "Transaction Type");
            $sheet->setCellValue('M5', "Short Text");
            $sheet->setCellValue('N5', "Cost Center");

            // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $sheet->getStyle('A5:N5')->applyFromArray($style_col);
            $sheet->getColumnDimension('A:N')->setAutoSize(true);
        }

        $rs = BukuBesarMdl::list($data);

        $row_idx = 6;
        $old_coaid = "";
        $balance = 0;
        $no = 1;
        $coa_from = $rs->fields['coacode'];
        while (!$rs->EOF)
        {
            $data_db = FieldsToObject($rs->fields);

            $debet = $data_db->debet;
            $credit = $data_db->credit;

            if ($old_coaid != $data_db->coaid && $data['with_bb'] == 't') $balance = $data_db->opbal;
            elseif ($old_coaid != $data_db->coaid && $data['with_bb'] == 'f') $balance = 0;

            $balance += $data_db->default_debet == 't' ? ($debet - $credit) : ($credit - $debet);
            $row_blank = $old_coaid != $data_db->coaid && $no > 1 ? 't' : 'f';
            $row_opening = $old_coaid != $data_db->coaid && $data['with_bb'] == 't' ? 't' : 'f';

            if ($coa_vs == 't')
            {
                $rsd = BukuBesarMdl::detail_jurnal($data_db->glid, $data_db->gldid);

                $data_db->coa_vs = $br = '';
                $mulai = false;
                while (!$rsd->EOF)
                {
                    if ($mulai) $br = "\n";

                    if ($rsd->fields['debet'] > 0) $stat_amount = 'Dr : '.format_uang($rsd->fields['debet'], 2);
                    else $stat_amount = 'Cr : '.format_uang($rsd->fields['credit'], 2);

                    $data_db->coa_vs .= $br.'- '.$rsd->fields['coacode'].' '.$rsd->fields['coaname'].' [ '.$stat_amount.' ]';
                    $mulai = true;

                    $rsd->MoveNext();
                }
            }

            if ($row_blank == 't')
            {
                $sheet->setCellValue('A'.$row_idx, '');
                $sheet->mergeCells('A'.$row_idx.':N'.$row_idx);

                $row_idx++;
            }

            if ($row_opening == 't')
            {
                $sheet->setCellValue('A'.$row_idx, $data_db->gldate);
                $sheet->setCellValue('B'.$row_idx, $data_db->coacode);
                $sheet->setCellValue('C'.$row_idx, $data_db->coaname);
                $sheet->setCellValue('D'.$row_idx, 'BEGINING BALANCE');
                $sheet->setCellValue('E'.$row_idx, '');

                if ($coa_vs == 't')
                {
                    $sheet->setCellValue('J'.$row_idx, floatval($data_db->opbal));
                    $sheet->setCellValue('K'.$row_idx, '');

                    $sheet->mergeCells('E'.$row_idx.':I'.$row_idx);
                    $sheet->mergeCells('K'.$row_idx.':O'.$row_idx);
                }
                else
                {
                    $sheet->setCellValue('I'.$row_idx, floatval($data_db->opbal));
                    $sheet->setCellValue('J'.$row_idx, '');

                    $sheet->mergeCells('E'.$row_idx.':H'.$row_idx);
                    $sheet->mergeCells('J'.$row_idx.':N'.$row_idx);
                }

                $row_idx++;
            }

            $sheet->setCellValue('A'.$row_idx, $data_db->gldate);
            $sheet->setCellValue('B'.$row_idx, $data_db->coacode);
            $sheet->setCellValue('C'.$row_idx, $data_db->coaname);
            $sheet->setCellValue('D'.$row_idx, $data_db->gldesc);

            if ($coa_vs == 't')
            {
                $sheet->getStyle('E'.$row_idx)
                      ->getNumberFormat()
                      ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                $sheet->setCellValueExplicit(
                    'E'.$row_idx,
                    $data_db->coa_vs,
                    DataType::TYPE_STRING
                );

                $sheet->setCellValue('F'.$row_idx, $data_db->nama_lengkap);
                $sheet->setCellValue('G'.$row_idx, $data_db->nama_supp);
                $sheet->setCellValue('H'.$row_idx, floatval($debet));
                $sheet->setCellValue('I'.$row_idx, floatval($credit));
                $sheet->setCellValue('J'.$row_idx, floatval($balance));
                $sheet->setCellValue('K'.$row_idx, $data_db->gldoc);
                $sheet->setCellValue('L'.$row_idx, $data_db->reff_code);
                $sheet->setCellValue('M'.$row_idx, $data_db->journal_name);
                $sheet->setCellValue('N'.$row_idx, $data_db->notes);
                $sheet->setCellValue('O'.$row_idx, $data_db->cost_center);

                // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                $sheet->getStyle('A'.$row_idx.':O'.$row_idx)->applyFromArray($style_row);

                $sheet->getStyle('E'.$row_idx)->getAlignment()->setWrapText(true);

                $sheet->getColumnDimension('E')->setAutoSize(true);

                $sheet->getRowDimension($row_idx)->setRowHeight(-1);
            }
            else
            {
                $sheet->setCellValue('E'.$row_idx, $data_db->nama_lengkap);
                $sheet->setCellValue('F'.$row_idx, $data_db->nama_supp);
                $sheet->setCellValue('G'.$row_idx, floatval($debet));
                $sheet->setCellValue('H'.$row_idx, floatval($credit));
                $sheet->setCellValue('I'.$row_idx, floatval($balance));
                $sheet->setCellValue('J'.$row_idx, $data_db->gldoc);
                $sheet->setCellValue('K'.$row_idx, $data_db->reff_code);
                $sheet->setCellValue('L'.$row_idx, $data_db->journal_name);
                $sheet->setCellValue('M'.$row_idx, $data_db->notes);
                $sheet->setCellValue('N'.$row_idx, $data_db->cost_center);

                // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                $sheet->getStyle('A'.$row_idx.':N'.$row_idx)->applyFromArray($style_row);
            }

            $no++;
            $row_idx++;
            $old_coaid = $data_db->coaid;
            $coa_to = $rs->fields['coacode'];

            $rs->MoveNext();
        }

        $sheet->setCellValue('A3', "C.O.A");
        $sheet->setCellValue('B3', ": ".$coa_from.' UNTIL '.$coa_to);
        $sheet->mergeCells('B3:E3');
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $sheet->setTitle("Buku Besar");

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Buku Besar.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    } /*}}}*/
}
?>