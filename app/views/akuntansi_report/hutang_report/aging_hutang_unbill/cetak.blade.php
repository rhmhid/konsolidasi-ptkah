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
    Laporan Aging Hutang ( Unbill )
    <span style="text-transform: uppercase;">Sampai Dengan : {{ $sdate }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Penerimaan</th>
            <th>Kode Penerimaan</th>
            <th>Nama Supplier</th>
            <th>No. Faktur / Surat Jalan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $hasData = false;
            $totall = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $hasData = true;
                $row = FieldsToObject($row);

                $totall += $row->nominal;
            @endphp

            <tr>
                <td align="center">{{ $no++ }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->grdate) }}</td>
                <td align="center">{{ $row->grcode }}</td>
                <td>{{ $row->nama_supp }}</td>
                <td align="center">{{ $row->no_faktur }}</td>
                <td align="right">{{ format_uang($row->nominal, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        @if ($hasData)
            <tr>
                <td colspan="5" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ $totall > 0 ? format_uang($totall, 2) : '' }}</b></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection