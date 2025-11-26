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
    Laporan Kas / Bank
    <span style="text-transform: uppercase;">Periode : {{ $sdate }} sd {{ $edate }}</span>
    <span style="text-transform: uppercase;">Kas / Bank : {{ $nama_bank }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Kode Transaksi</th>
            <th>No. Jurnal</th>
            <th>Tipe Jurnal</th>
            <th>Keterangan</th>
            <th>Status Posting</th>
            <th>Status Oleh</th>
            <th>Nominal</th>
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

                $status_posted = $row->is_posted == 't' ? 'POSTED' : 'NOT POSTED';
                $saldo_akhir = $saldo_awal + $row->nominal;
            @endphp

            @if ($no == 1)
                <tr>
                    <td align="center">&nbsp;</td>
                    <td align="center">{{ $sdate }}</td>
                    <td colspan="2">&nbsp;</td>
                    <td>BEGINING BALANCE</td>
                    <td align="center" colspan="4">&nbsp;</td>
                    <td align="right">{{ format_uang($saldo_awal, 2) }}</td>
                </tr>
            @endif

            <tr>
                <td align="center">{{ $no }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                <td align="center">{{ $row->reff_code }}</td>
                <td align="center">{{ $row->gldoc }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td>{{ $row->notes }}</td>
                <td align="center">{{ $status_posted }}</td>
                <td align="center">{{ $row->user_posting }}</td>
                <td align="right">{{ format_uang($row->nominal, 2) }}</td>
                <td align="right">{{ format_uang($saldo_akhir, 2) }}</td>
            </tr>

            @php
                $no++;
                $saldo_awal = $saldo_akhir;
            @endphp
        @empty
            <tr>
                <td colspan="10" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection