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
    Laporan Penerimaan Barang
    <span style="text-transform: uppercase;">Periode : {{ $periode }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Terima</th>
            <th>Tanggal Terima</th>
            <th>No. Faktur</th>
            <th>Kode PO</th>
            <th>Gudang</th>
            <th>Supplier</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Diskon</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $barang = $row->kode_brg.' - '.$row->nama_brg;
                $jumlah = floatval($row->vol).' '.$row->nama_satuan;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td align="center">{{ $row->grcode }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->grdate) }}</td>
                <td align="center">{{ $row->no_faktur }}</td>
                <td align="center">{{ $row->pocode }}</td>
                <td align="center">{{ $row->nama_gudang }}</td>
                <td align="center">{{ $row->nama_supp }}</td>
                <td>{{ $barang }}</td>
                <td align="center">{{ $jumlah }}</td>
                <td align="right">{{ format_uang($row->harga, 2) }}</td>
                <td align="right">{{ format_uang($row->disc_rp, 2) }}</td>
                <td align="right">{{ format_uang($row->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection