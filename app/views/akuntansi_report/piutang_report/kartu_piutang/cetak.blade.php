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
    Laporan Kartu Piutang
    <span style="text-transform: uppercase;">Periode : {{ $sdate }} sd {{ $edate }}</span>
    <span style="text-transform: uppercase;">Supplier :{!! $nama_customer !!}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No</th>
            <th>Type Trans</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Saldo Awal</th>
            <th>Nominal Invoice</th>
            <th>Nominal Payment</th>
            <th>Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp

        @forelse ($rs as $row)
            @php
                $row = FieldsToObject($row);

                $saldo_akhir = $saldo_awal + $row->nominal_inv - $row->nominal_pay;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                <td>{{ $row->notes }}</td>
                <td align="right">{{ format_uang($saldo_awal, 2) }}</td>
                <td align="right">{{ format_uang($row->nominal_inv, 2)}}</td>
                <td align="right">{{ format_uang($row->nominal_pay, 2)}}</td>
                <td align="right">{{ format_uang($saldo_akhir, 2)}}</td>
            </tr>

            @php
                $no++;
                $saldo_awal = $saldo_akhir;
            @endphp
        @empty
            <tr>
                <td colspan="8" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection