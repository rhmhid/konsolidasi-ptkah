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
    Laporan Pembayaran Hutang
    <span style="text-transform: uppercase;">Periode : {{ $sdate }} sd {{ $edate }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Supplier</th>
            <th rowspan="2">Bank</th>
            <th rowspan="2">Cara Bayar</th>
            <th rowspan="2">Tanggal Bayar</th>
            <th rowspan="2">No. Bayar</th>
            <th rowspan="2">Keterangan</th>
            <th rowspan="2">Pembayaran</th>
            <th colspan="2">Add/Less</th>
            <th rowspan="2">Potongan</th>
            <th rowspan="2">Pembulatan</th>
            <th rowspan="2">Other Cost</th>
            <th rowspan="2">Subtotal</th>
        </tr>
        <tr>
            <th>Debet</th>
            <th>credit</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $totpay = $totaldeb = $totalcre = $totpot = $totround = $totother = $totall = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $row = FieldsToObject($row);

                $row->nama_supp .= $row->suppid == -1 ? ' <i style="color: red">[ '.$row->nama_dokter.' ]</i>' : '';
                $subtotal = $row->pembayaran + $row->al_debet - $row->al_credit - $row->potongan + $row->pembulatan + $row->other_cost;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td>{!! $row->nama_supp !!}</td>
                <td>{{ $row->bank_nama }}</td>
                <td align="center">{{ GetCaraBayar($row->cara_bayar) }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->paydate) }}</td>
                <td align="center">{{ $row->no_bayar }}</td>
                <td>{{ $row->keterangan }}</td>
                <td align="right">{{ format_uang($row->pembayaran, 2) }}</td>
                <td align="right">{{ format_uang($row->al_debet, 2) }}</td>
                <td align="right">{{ format_uang($row->al_credit, 2) }}</td>
                <td align="right">{{ format_uang($row->potongan, 2) }}</td>
                <td align="right">{{ format_uang($row->pembulatan, 2) }}</td>
                <td align="right">{{ format_uang($row->other_cost, 2) }}</td>
                <td align="right">{{ format_uang($subtotal, 2) }}</td>
            </tr>

            @php
                $no++;
                $totpay += $row->pembayaran;
                $totaldeb += $row->al_debet;
                $totalcre += $row->al_credit;
                $totpot += $row->potongan;
                $totround += $row->pembulatan;
                $totother += $row->other_cost;
                $totall += $subtotal;
            @endphp
        @empty
            <tr>
                <td colspan="8" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        <tr>
            <td align="right" colspan="7"><b>TOTAL</b></td>
            <td align="right"><b>{{ format_uang($totpay, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totaldeb, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totalcre, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totpot, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totround, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totother, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($totall, 2) }}</b></td>
        </tr>
    </tbody>
</table>
@endsection