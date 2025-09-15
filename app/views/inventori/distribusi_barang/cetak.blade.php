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
        <span class="til">Kode Trans</span><span class="num">{{ $data_db->transfer_code }}</span>
    </span>

    BUKTI DISTRIBUSI BARANG
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->transfer_date) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Gudang Pengirim</td>
            <td width="20%">: {{ $data_db->pengirim }}</td>
            <td width="10%">Gudang Penerima</td>
            <td width="20%">: {{ $data_db->penerima }}</td>
        </tr>
        <tr>
            <td>Petugas</td>
            <td>: {{ $data_db->petugas }}</td>
            <td>Keterangan</td>
            <td>: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Barang</th>
            <th width="10%">Jumlah</th>
            <th width="15%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);
            @endphp

            <tr>
                <td align="center">{{ ++$no }}</td>
                <td>{{ $row->kode_brg }} {{ $row->nama_brg }}</td>
                <td align="center">{{ floatval($row->vol) }} {{ $row->kode_satuan }}</td>
                <td>{{ $row->ket_item }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('sign')
<div id="ftr" class="tp2">
    <div class="wpr">
        <div>
            <p align="center">
                <br />
                Pengirim
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
                Penerima
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