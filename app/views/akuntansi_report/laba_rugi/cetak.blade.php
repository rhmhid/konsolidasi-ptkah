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

@push('kop')
<div id="lhd">
    <div class="ttl">
        @if (isMultiTenants() == 't')
        <span class="nme">{{ Auth::user()->branch->branch_name }}</span>
        <span>
            {{ Auth::user()->branch->branch_addr }}
        </span>
        @else
        <span class="nme">{{ dataConfigs('company_name') }}</span>
        <span>
            {{ dataConfigs('company_address') }}, Phone : {{ dataConfigs('company_telp') }}, {{ dataConfigs('company_city') }} - {{ dataConfigs('company_provinsi') }}
        </span>
        @endif
    </div>
</div>
@endpush

@section('content')
<h2 class="bdr">
    Profit And Loss
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

@php
    $jml_bid = 1;
@endphp
<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Coacode</th>
            <th rowspan="2">Coaname</th>
            <th rowspan="2">Dr / Cr Position</th>

            @foreach ($data_cabang as $head)
                @php
                    $head = FieldsToObject($head);
                    $jml_bid++;
                @endphp

                <th colspan="3">{{ $head->branch_name }}</th>
            @endforeach

            <th colspan="3">Total</th>
        </tr>

        <tr>
            @for ($idx = 1; $idx <= $jml_bid; $idx++)
                <th>{{ $bln }}</th>
                <th>{{ $bln_prev }}</th>
                <th>Until {{ $bln }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($data_pl as $row)
            @php
                $row = FieldsToObject($row);

                $subtotal_header[$row->coatid]['headname'] = $row->coatid == 4 ? 'INCOME' : 'COST';

                $colspan = 7 + (($jml_bid - 1) * 3);
            @endphp

            <tr>
                <td align="center">{{ $nomor }}</td>
                <td align="center">{{ $row->coacode }}</td>
                <td>{{ $row->coaname }}</td>
                <td align="center">{{ $row->posisi }}</td>

                @foreach ($data_cabang as $cab)
                    @php
                        $cab = FieldsToObject($cab);
                        $bcode = $cab->branch_code;

                        $row->amount_bln_prev += $row->branch[$bcode]['amount_bln_prev'];
                        $row->amount_bln += $row->branch[$bcode]['amount_bln'];
                        $row->closingbal += $row->branch[$bcode]['closingbal'];
                    @endphp

                    <td align="right">{{ format_uang($row->branch[$bcode]['amount_bln_prev'], 2) }}</td>
                    <td align="right">{{ format_uang($row->branch[$bcode]['amount_bln'], 2) }}</td>
                    <td align="right">{{ format_uang($row->branch[$bcode]['closingbal'], 2) }}</td>
                @endforeach

                <td align="right">{{ format_uang($row->amount_bln_prev, 2) }}</td>
                <td align="right">{{ format_uang($row->amount_bln, 2) }}</td>
                <td align="right">{{ format_uang($row->closingbal, 2) }}</td>
            </tr>

            @if ($coacode_last[$row->coatid] == $row->coacode)
                <tr>
                    <td colspan="4"><b>SUBTOTAL {{ $subtotal_header[$row->coatid]['headname'] }}</b></td>

                    @foreach ($data_cabang as $cab)
                        @php
                            $cab = FieldsToObject($cab);
                            $bcode = $cab->branch_code;

                            $subtotal_header[$row->coatid][$bcode]['amount_bln_prev']   += $row->branch[$bcode]['amount_bln_prev'];
                            $subtotal_header[$row->coatid][$bcode]['amount_bln']        += $row->branch[$bcode]['amount_bln'];
                            $subtotal_header[$row->coatid][$bcode]['closingbal']        += $row->branch[$bcode]['closingbal'];

                            $subtotal[$row->coatid]['amount_bln_prev']    += $row->branch[$bcode]['amount_bln_prev'];
                            $subtotal[$row->coatid]['amount_bln']         += $row->branch[$bcode]['amount_bln'];
                            $subtotal[$row->coatid]['closingbal']         += $row->branch[$bcode]['closingbal'];
                        @endphp

                        <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid][$bcode]['amount_bln'], 2) }}</b></td>
                        <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid][$bcode]['amount_bln_prev'], 2) }}</b></td>
                        <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid][$bcode]['closingbal'], 2) }}</b></td>
                    @endforeach

                    <td align="right"><b>{{ format_uang($subtotal[$row->coatid]['amount_bln_prev'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotal[$row->coatid]['amount_bln'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotal[$row->coatid]['closingbal'], 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="{{ $colspan }}"><b>&nbsp;</b></td>
                </tr>
            @endif

            @php
                $nomor++;
                if ($coacode_last[$row->coatid] == $row->coacode) $nomor = 1;
            @endphp

            @foreach ($data_cabang as $cab)
                @php
                    $cab = FieldsToObject($cab);
                    $bcode = $cab->branch_code;

                    if ($row->coatid == 4)
                    {
                        $balance_prev[$bcode]['income'] += $row->branch[$bcode]['amount_bln_prev'];
                        $balance_bln[$bcode]['income'] += $row->branch[$bcode]['amount_bln'];
                        $balance[$bcode]['income'] += $row->branch[$bcode]['closingbal'];
                    }

                    if ($row->coatid != 4)
                    {
                        $balance_prev[$bcode]['cost'] += $row->branch[$bcode]['amount_bln_prev'];
                        $balance_bln[$bcode]['cost'] += $row->branch[$bcode]['amount_bln'];
                        $balance[$bcode]['cost'] += $row->branch[$bcode]['closingbal'];
                    }
                @endphp
            @endforeach
        @endforeach

        <tr>
            <td colspan="4"><b>PROFIT AND LOSS</b></td>

            @foreach ($data_cabang as $cab)
                @php
                    $cab = FieldsToObject($cab);
                    $bcode = $cab->branch_code;

                    $subtot_bln_prev[$bcode] += $balance_prev[$bcode]['income'] - $balance_prev[$bcode]['cost'];
                    $subtot_bln[$bcode] += $balance_bln[$bcode]['income'] - $balance_bln[$bcode]['cost'];
                    $subtot_thn[$bcode] += $balance[$bcode]['income'] - $balance[$bcode]['cost'];
                @endphp

                <td align="right"><b>{{ format_uang($subtot_bln_prev[$bcode], 2)}}</b></td>
                <td align="right"><b>{{ format_uang($subtot_bln[$bcode], 2)}}</b></td>
                <td align="right"><b>{{ format_uang($subtot_thn[$bcode], 2)}}</b></td>
            @endforeach

            <td align="right"><b>{{ format_uang($subtot_bln_prev[$bcode], 2)}}</b></td>
            <td align="right"><b>{{ format_uang($subtot_bln[$bcode], 2)}}</b></td>
            <td align="right"><b>{{ format_uang($subtot_thn[$bcode], 2)}}</b></td>
        </tr>
    </tbody>
</table>
@endsection