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

@push('css')
<script type="text/javascript">
    function detail_coa (pplid)
    {
        let $param = 'sdate={{ $data['sdate'] }}'
            $param += '&edate={{ $data['edate'] }}'

        let $link = "{{ route('akuntansi_report.laba_rugi.detail_coa.cetak', ['mytipe' => ':mytipe', 'myid' => ':myid']) }}"
            $link = $link.replace(':mytipe', '{{ $mytipe }}')
            $link = $link.replace(':myid', pplid)

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
        LAPORAN REKAP RUGI / LABA DAILY<br />
        PER {{ strtoupper($sdate) }} DAN {{ strtoupper($edate) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="pad">
    <thead>
        <tr>
            <th>Keterangan</th>
            <th>{{ $sdate }} sd {{ $edate }}</th>
            <th>Until {{ $edate }}</th>
        </tr>
    </thead>
    <tbody>
        @if (!$empty_pos)
            @foreach ($data_pos as $rec)
                @php
                    $rec = FieldsToObject($rec);
                @endphp

                <tr style="background: {{ $rec->color }};">
                    <td>{!! $rec->nama_pos !!}</td>
                    <td align="right">{!! $rec->amount_period !!}</td>
                    <td align="right">{!! $rec->amount_untill !!}</td>
                </tr>
            @endforeach
        @endif

        @if (!$without_mapping)
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr style="background: #F2ECEC">
            <td><b>POS LABA/RUGI LAINNYA</b></td>
            <td align="right"><b><u>{!! $pos_amount_period !!}</u></b></td>
            <td align="right"><b><u>{!! $pos_amount_untill !!}</u></b></td>
        </tr>
        @endif
    </tbody>
</table>
@endsection