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
        LAPORAN RUGI / LABA DAILY DETAIL COA <br />
        PER {{ $sdate }} DAN {{ $edate }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="pad">
    <thead>
        <tr>
            <th>C.O.A</th>
            <th>{{ $sdate }} sd {{ $edate }}</th>
            <th>Until {{ $edate }}</th>
        </tr>
    </thead>
    <tbody>
        @if (!$empty_pos)
            @foreach ($data_db as $coaid => $rec)
                @php
                    $rec = FieldsToObject($rec);

                    $tot_amount_period += $rec->amount_period;
                    $tot_amount_untill += $rec->amount_untill;
                @endphp

                <tr>
                    <td>{{ $rec->coa }}</td>
                    <td align="right">{{ format_uang($rec->amount_period, 2) }}</td>
                    <td align="right">{{ format_uang($rec->amount_untill, 2) }}</td>
                </tr>
            @endforeach
        @endif

        <tr>
            <td align="right"><b>TOTAL<b></td>
            <td align="right"><b>{{ format_uang($tot_amount_period, 2) }}<b></td>
            <td align="right"><b>{{ format_uang($tot_amount_untill, 2) }}<b></td>
        </tr>
    </tbody>
</table>
@endsection