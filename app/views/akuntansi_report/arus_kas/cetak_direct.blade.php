@extends('layouts.print')

@push('css')
<style type="text/css">
    table tr th { vertical-align: middle; text-align: center; }
    h2 span { font-weight: bold; }
</style>

<style media="print">
    body { margin-top: -2cm; }
    table { border-collapse: unset; }
</style>
@endpush

@push('script')
<script type="text/javascript">
    function detail_coa (pcfid)
    {
        let $mytipe = '{{ $mytipe }}'

        let $param = 'month={{ $data['month'] }}'
            $param += '&year={{ $data['year'] }}'
            $param += '&sdate={{ $data['sdate'] }}'
            $param += '&edate={{ $data['edate'] }}'
            $param += '&bid={{ $data['bid'] }}'
            $param += '&status_cabang={{ $data['status_cabang'] }}'
            $param += '&status_coa={{ $data['status_coa'] }}'

        let $link = "{{ route('akuntansi_report.arus_kas.cetak.detail', ['mytipe' => ':mytipe', 'myid' => ':myid']) }}"
            $link = $link.replace(':mytipe', $mytipe)
            $link = $link.replace(':myid', pcfid)

        popFullScreen2($link + '?' + $param)
    }
</script>
@endpush

@push('function')
<div id="functions">
    <ul>
        <li><a href="javascript:void(0)" onclick="JavaScript:window.print();">Print</a></li>
        <li><a href="javascript:void(0)" onclick="JavaScript:window.close();">Close</a></li>
    </ul>        
</div>
@endpush

@section('content')
<h2 class="bdr ctr">
    <p style="line-height: 1.2em;">
        @if (isMultiTenants() == 't')
            {{ Auth::user()->branch->branch_name }}
        @else
            {{ dataConfigs('company_name') }}
        @endif<br />
        LAPORAN ARUS KAS ( METODE DIRECT ) {{ $subtitle }}<br />
        UNTUK PERIODE YANG BERAKHIR {{ strtoupper($periode) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">Keterangan</th>
            @foreach ($data_cabang as $bc => $cabang)
                <th colspan="3">{{ $cabang['branch_name'] }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th colspan="3">Total All Branch</th>
            @endif
        </tr>
        <tr>
            @foreach ($data_cabang as $bc => $cabang)
                <th>Detail</th>
                <th>Sub Total</th>
                <th>Total</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th>Detail</th>
                <th>Sub Total</th>
                <th>Total</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $amount_cf = [
                'branches'  => [],
                'total'     => 0
            ];
        @endphp

        @if (!$empty_pos)
            @foreach ($data_pos as $row)
                @php
                    $row = FieldsToObject($row);
                    $amounts = json_decode(json_encode($row->amounts), true);
                @endphp

                <tr style="background: {{ $row->background }}">
                    <td>{!! $row->nama_pos !!}</td>

                    @foreach ($data_cabang as $bc => $cabang)
                        @php
                            if ($row->level == 0)
                                $amount_cf['branches'][$bc] = ($amount_cf['branches'][$bc] ?? 0) + ($amounts['branches'][$bc]['raw_amount'] ?? 0);
                        @endphp

                        <td width="7%" align="right">{!! $amounts['branches'][$bc]['amount_detail'] ?? '' !!}</td>
                        <td width="7%" align="right">{!! $amounts['branches'][$bc]['amount_subheader'] ?? '' !!}</td>
                        <td width="7%" align="right">{!! $amounts['branches'][$bc]['amount_header'] ?? '' !!}</td>
                    @endforeach

                    @if(count($data_cabang) > 1)
                        @php
                            if ($row->level == 0)
                                $amount_cf['total'] += ($amounts['total']['raw_amount'] ?? 0);
                        @endphp

                        <td width="7%" align="right">{!! $amounts['total']['amount_detail'] !!}</td>
                        <td width="7%" align="right">{!! $amounts['total']['amount_subheader'] !!}</td>
                        <td width="7%" align="right">{!! $amounts['total']['amount_header'] !!}</td>
                    @endif
                </tr>
            @endforeach
        @endif

        <tr style="background: #D9D9D9;">
            <td><b>Net Incerease and Decrease in Cash and Cash Equivalents</b></td>

            @foreach ($data_cabang as $bc => $cabang)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($amount_cf['branches'][$bc] ?? 0, 2) !!}</u></b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($amount_cf['total'], 2) !!}</u></b></td>
            @endif
        </tr>

        <tr style="background: #D9D9D9;">
            <td><b>Cash and Cash Equivalents at Beginning of Period</b></td>

            @foreach ($data_cabang as $bc => $cabang)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($saldo['branches'][$bc]['cf_speriod'] ?? 0, 2) !!}</u></b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($saldo['total']['cf_speriod'] ?? 0, 2) !!}</u></b></td>
            @endif
        </tr>

        <tr style="background: #D9D9D9;">
            <td><b>Cash and Cash Equivalents at End of Period</b></td>

            @foreach ($data_cabang as $bc => $cabang)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($saldo['branches'][$bc]['cf_eperiod'] ?? 0, 2) !!}</u></b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td colspan="2"><b>&nbsp;</b></td>
                <td align="right"><b><u>{!! format_uang($saldo['total']['cf_eperiod'] ?? 0, 2) !!}</u></b></td>
            @endif
        </tr>

        @if (!$without_mapping)
        <tr>
            @php
                $colspan_kosong = 1 + (count($data_cabang) * 3) + (count($data_cabang) > 1 ? 3 : 0);
            @endphp

            <td colspan="{{ $colspan_kosong }}">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <a href="javascript:void(0)" onclick="detail_coa(0);">
                    <b>POS ARUS KAS LAINNYA</b>
                </a>
            </td>

            @foreach ($data_cabang as $bc => $cabang)
                <td colspan="2">&nbsp;</td>
                <td align="right"><b><u>{!! $pos_lainnya['branches'][$bc] ?? '' !!}</u></b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td colspan="2">&nbsp;</td>
                <td align="right"><b><u>{!! $pos_lainnya['total'] !!}</u></b></td>
            @endif

        </tr>
        @endif
    </tbody>
</table>
@endsection