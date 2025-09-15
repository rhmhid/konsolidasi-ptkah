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

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Coacode</th>
            <th>Coaname</th>
            <th>Dr / Cr Position</th>
            <th>{{ $bln }}</th>
            <th>{{ $bln_prev }}</th>
            <th>Until {{ $bln }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data_pl as $row)
            @php
                $row = FieldsToObject($row);

                $subtotal_header[$row->coatid]['headname']          = $row->coatid == 4 ? 'INCOME' : 'COST';
                $subtotal_header[$row->coatid]['amount_bln_prev']   += $row->amount_bln_prev;
                $subtotal_header[$row->coatid]['amount_bln']        += $row->amount_bln;
                $subtotal_header[$row->coatid]['closingbal']        += $row->closingbal;
            @endphp
            <tr>
                <td align="center">{{ $nomor }}</td>
                <td align="center">{{ $row->coacode }}</td>
                <td>{{ $row->coaname }}</td>
                <td align="center">{{ $row->posisi }}</td>
                <td align="right">{{ format_uang($row->amount_bln, 2)}}</td>
                <td align="right">{{ format_uang($row->amount_bln_prev, 2)}}</td>
                <td align="right">{{ format_uang($row->closingbal, 2)}}</td>
            </tr>

            @if ($coacode_last[$row->coatid] == $row->coacode)
                <tr>
                    <td colspan="4"><b>SUBTOTAL {{ $subtotal_header[$row->coatid]['headname'] }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid]['amount_bln'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid]['amount_bln_prev'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotal_header[$row->coatid]['closingbal'], 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="7"><b>&nbsp;</b></td>
                </tr>
            @endif

            @php
                $nomor++;
                if ($coacode_last[$row->coatid] == $row->coacode) $nomor = 1;

                if ($row->coatid == 4)
                {
                    $balance_income_prev += $row->amount_bln_prev;
                    $balance_income_bln += $row->amount_bln;
                    $balance_income += $row->closingbal;
                }

                if ($row->coatid != 4)
                {
                    $balance_cost_prev += $row->amount_bln_prev;
                    $balance_cost_bln += $row->amount_bln;
                    $balance_cost += $row->closingbal;
                }
            @endphp
        @endforeach

        @php
            $tot_pnl_bln_prev = $balance_income_prev - $balance_cost_prev;
            $tot_pnl_bln = $balance_income_bln - $balance_cost_bln;
            $tot_pnl_thn = $balance_income - $balance_cost;
        @endphp

        <tr>
            <td colspan="4"><b>TOTAL LABA / RUGI</b></td>
            <td align="right"><b>{{ format_uang($tot_pnl_bln, 2)}}</b></td>
            <td align="right"><b>{{ format_uang($tot_pnl_bln_prev, 2)}}</b></td>
            <td align="right"><b>{{ format_uang($tot_pnl_thn, 2)}}</b></td>
        </tr>
    </tbody>
</table>
@endsection