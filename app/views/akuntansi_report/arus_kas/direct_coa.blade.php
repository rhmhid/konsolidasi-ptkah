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
        DETAIL COA CASHFLOW {{ $subtitle }}<br />
        PER {{ strtoupper($periode) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
    <span style="text-transform: uppercase;">Pos : {{ $cf_name }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>Tanggal Transaksi</th>
            <th>C.O.A</th>
            <th>No. Doc</th>
            <th>Type Transaksi</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Amount</th>
            <th>user Entry</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $row = FieldsToObject($row);

                $tot_amount += $row->amount;
            @endphp

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                <td>{{ $row->coacode.' '.$row->coaname }}</td>
                <td align="center">{{ $row->doc_no }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td>{{ $row->gldesc }}</td>
                <td>{{ $row->notes }}</td>
                <td align="right">{{ format_uang($row->amount, 2) }}</td>
                <td>{{ $row->user_input }}</td>
            </tr>
        @endforeach

        <tr>
            <td align="right" colspan="6"><b>TOTAL<b></td>
            <td align="right"><b>{{ format_uang($tot_amount, 2) }}<b></td>
            <td align="right"><b><b></td>
        </tr>
    </tbody>
</table>
@endsection