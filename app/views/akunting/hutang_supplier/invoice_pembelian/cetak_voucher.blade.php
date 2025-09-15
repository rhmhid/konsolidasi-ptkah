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
        <span class="til">No. A/P</span><span class="num">{{ $data_db->apcode }}</span>
    </span>

    VOUCHER PENGAJUAN PEMBAYARAN KAS / BANK
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->apdate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Nama Supplier</td>
            <td>: {{ $data_db->nama_supp }} {!! $nama_dokter !!}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Duedate</th>
            <th>Keterangan</th>
            <th>Invoice</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $total = floatval($row->amount);
            @endphp

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->apdate) }}</td>
                <td align="center">{{ dbtstamp2stringina($row->duedate) }}</td>
                <td align="center">{{ $row->no_invoice }}</td>
                <td>{{ $row->keterangan }}</td>
                <td align="right">Rp. {{ format_uang($row->amount, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($total, 2) }}</b></td>
        </tr>
        <tr>
            <td align="center" colspan="5"><b># {{ strtoupper(terbilang($total)) }} RUPIAH #</b></td>
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