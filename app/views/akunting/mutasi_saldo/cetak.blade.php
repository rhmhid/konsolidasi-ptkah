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
        <span class="til">Nomor</span>
        <span class="num">{{ $data_db->mutasi_code }}</span>
    </span>

    MUTASI SALDO
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->mutasi_date) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Petugas</td>
            <td width="20%">: {{ $data_db->useri }}</td>
            <td width="10%">Keterangan</td>
            <td width="20%">: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th>Kas / Bank Asal</th>
            <th>Kas / Bank Tujuan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $data_db->bank_from }}</td>
            <td>{{ $data_db->bank_to }}</td>
            <td align="right">Rp. {{ format_uang($data_db->amount, 2) }}</td>
        </tr>
        <tr>
            <td align="center" colspan="4"><b># {{ Terbilang_rp($data_db->amount) }} #</b></td>
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