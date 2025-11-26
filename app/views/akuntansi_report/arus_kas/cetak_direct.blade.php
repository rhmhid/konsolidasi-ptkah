@extends('layouts.print')

@push('css')
<style type="text/css">
    table tr th { vertical-align: middle; }
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
    <tbody>
        @if (!$empty_pos)
            @foreach ($data_pos as $row)
                @php
                    $row = FieldsToObject($row);
                @endphp

                <tr style="background: {{ $row->background }}">
                    <td>{!! $row->nama_pos !!}</td>
                    <td width="15%" align="right">{!! $row->amount_detail !!}</td>
                    <td width="15%" align="right">{!! $row->amount_subheader !!}</td>
                    <td width="15%" align="right">{!! $row->amount_header !!}</td>
                </tr>
            @endforeach
        @endif

        <tr style="background: #D9D9D9;">
            <td><b>Net Incerease and Decrease in Cash and Cash Equivalents</b></td>
            <td colspan="2"><b>&nbsp;</b></td>
            <td align="right"><b><u>{!! format_uang($amount_cf, 2) !!}</u></b></td>
        </tr>
        <tr style="background: #D9D9D9;">
            <td><b>Cash and Cash Equivalents at Beginning of Period</b></td>
            <td colspan="2"><b>&nbsp;</b></td>
            <td align="right"><b><u>{!! format_uang($cf_speriod, 2) !!}</u></b></td>
        </tr>
        <tr style="background: #D9D9D9;">
            <td><b>Cash and Cash Equivalents at End of Period</b></td>
            <td colspan="2"><b>&nbsp;</b></td>
            <td align="right"><b><u>{!! format_uang($cf_eperiod, 2) !!}</u></b></td>
        </tr>

        @if (!$without_mapping)
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">
                <a href="javascript:void(0)" onclick="detail_coa(0);">
                    <b>POS ARUS KAS LAINNYA</b>
                </a>
            </td>
            <td align="right"><b><u>{!! $pos_amount !!}</u></b></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection