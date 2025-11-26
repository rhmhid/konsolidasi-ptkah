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
    Kartu Stok
    <span style="text-transform: uppercase;">Periode : {{ $periode }}</span>
    <span style="text-transform: uppercase;">Gudang : {{ $gudang }}</span>
    <span style="text-transform: uppercase;">Barang : {{ $barang }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Type Transaksi</th>
            <th>Tanggal Transaksi</th>
            <th>Keterangan</th>
            <th>Awal</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Adjusment</th>
            <th>Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $akhir = $awal + $row->masuk + $row->keluar + $row->adj;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->invdate) }}</td>
                <td>{{ $row->detailnote }}</td>
                <td align="center">{{ floatval($awal) }}</td>
                <td align="center">{{ floatval($row->masuk) }}</td>
                <td align="center">{{ floatval($row->keluar) }}</td>
                <td align="center">{{ floatval($row->adj) }}</td>
                <td align="center">{{ floatval($akhir) }}</td>
            </tr>

            @php
                $awal = $akhir;
            @endphp
        @endforeach
    </tbody>
</table>
@endsection