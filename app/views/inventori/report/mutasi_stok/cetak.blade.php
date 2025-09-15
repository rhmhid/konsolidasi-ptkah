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
    Laporan Mutasi Stok
    <span style="text-transform: uppercase;">Periode : {{ $periode }}</span>
    <span style="text-transform: uppercase;">Barang : {{ $barang }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">Tanggal Transaksi</th>
            <th rowspan="2">No. Transaksi</th>
            <th rowspan="2">Type Transaksi</th>
            <th rowspan="2">No. Referensi</th>
            <th rowspan="2">User</th>
            <th rowspan="2">Satuan</th>
            <th rowspan="2">WAC</th>
            <th colspan="2">Gudang</th>
            <th colspan="4">Jumlah</th>
            <th colspan="2">Saldo</th>
        </tr>
        <tr>
            <th>Dari</th>
            <th>Ke</th>
            <th>Jumlah Keluar</th>
            <th>Nominal Keluar</th>
            <th>Jumlah Masuk</th>
            <th>Nominal Masuk</th>
            <th>Jumlah</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="right" colspan="13"><b>Saldo Awal</b></td>
            <td align="center"><b>{{ floatval($vol_awal) }}</b></td>
            <td align="right"><b>{{ format_uang($amount_awal, 2) }}</b></td>
        </tr>

        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $vol_masuk = $row->vol_masuk ? floatval($row->vol_masuk) : '';
                $amount_masuk = $row->amount_masuk ? format_uang($row->amount_masuk, 2) : '';

                $vol_keluar = $row->vol_keluar ? floatval($row->vol_keluar) : '';
                $amount_keluar = $row->amount_keluar ? format_uang($row->amount_keluar, 2) : '';

                $vol_akhir = $vol_awal + $row->vol_keluar + $row->vol_masuk;
                $amount_akhir = $amount_awal + $row->amount_keluar + $row->amount_masuk;
            @endphp

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->invdate) }}</td>
                <td align="center">{{ $row->invcode }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td align="center">{{ $row->reff_code }}</td>
                <td>{{ $row->user }}</td>
                <td align="center">{{ $row->kode_satuan }}</td>
                <td align="center">{{ format_uang($row->wac, 2) }}</td>
                <td>{{ $row->pengirim }}</td>
                <td>{{ $row->penerima }}</td>
                <td align="center">{{ $vol_keluar }}</td>
                <td align="right">{{ $amount_keluar }}</td>
                <td align="center">{{ $vol_masuk }}</td>
                <td align="right">{{ $amount_masuk }}</td>
                <td align="center">{{ floatval($vol_akhir) }}</td>
                <td align="right">{{ format_uang($amount_akhir, 2) }}</td>
            </tr>

            @php
                $vol_awal = $vol_akhir;
                $amount_awal = $amount_akhir;
            @endphp
        @endforeach

        <tr>
            <td colspan="13" align="right"><b>Saldo Akhir</b></td>
            <td align="center"><b>{{ floatval($vol_akhir) }}</b></td>
            <td align="right"><b>{{ format_uang($amount_akhir, 2) }}</b></td>
        </tr>
    </tbody>
</table>
@endsection