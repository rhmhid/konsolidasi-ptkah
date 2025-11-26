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
    <span class="rgt">
        <span class="til">Kode Trans</span><span class="num">{{ $data_db->pccode }}</span>
    </span>

    BUKTI TRANSAKSI KAS/BANK
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->pcdate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Petugas</td>
            <td width="20%">: {{ $data_db->petugas }}</td>
            <td width="10%">Cash Book</td>
            <td width="20%">: {{ $data_db->cash_book }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="3">: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th width="5%" rowspan="2">No</th>
            <th width="10%" rowspan="2">Transaction Type</th>
            <th width="10%" rowspan="2">Description</th>
            <th colspan="2">Amount</th>
            <th width="10%" rowspan="2">Cost Center</th>
        </tr>
        <tr>
            <th width="5%">Debet</th>
            <th width="5%">Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $tot_deb += $row->debet;
                $tot_cre += $row->credit;
            @endphp

            <tr>
                <td align="center">{{ ++$no }}</td>
                <td>{{ $row->ket_trans }}</td>
                <td>{{ $row->notes }}</td>
                <td align="right">Rp. {{ format_uang($row->debet, 2) }}</td>
                <td align="right">Rp. {{ format_uang($row->credit, 2) }}</td>
                <td>{{ $row->cost_center }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($tot_deb, 2) }}</b></td>
            <td align="right"><b>Rp. {{ format_uang($tot_cre, 2) }}</b></td>
            <td><b>&nbsp;</b></td>
        </tr>
    </tbody>
</table>
@endsection

@push('sign')
<div id="ftr" class="tp2">
    <div class="wpr">
        <div>
            <p align="center">
                <br />
                <br />
                <br />
                <br />
                <br />
                (
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                )
            </p>
        </div>
    </div>

    <div class="wpr">
        <div>
            <p align="center">
                {{ dataConfigs('company_city') }}, {{ $now }}
                <br />
                <br />
                <br />
                <br />
                <br />
                (
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                )
            </p>
        </div>
    </div>
</div>
@endpush