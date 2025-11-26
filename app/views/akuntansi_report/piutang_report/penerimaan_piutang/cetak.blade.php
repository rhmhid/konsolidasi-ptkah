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
    Laporan Penerimaan Piutang
    <span style="text-transform: uppercase;">Periode : {{ $sdate }} sd {{ $edate }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Customer</th>
            <th>Bank</th>
            <th>Cara Terima</th>
            <th>Tanggal Terima</th>
            <th>No. Terima</th>
            <th>Keterangan</th>
            <th>Penerimaan</th>
            <th>Potongan</th>
            <th>Pembulatan</th>
            <th>Biaya Lainnya</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $hasData = false;
            $tot_penerimaan = $tot_potongan = $tot_pembulatan = $tot_other_cost = $tot_all = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $row = FieldsToObject($row);

                $hasData = true;
                $row->nama_customer .= $row->custid == -1 ? ' <i style="color: red">[ '.$row->nama_lengkap.' ]</i>' : '';
                $row->nama_customer .= $row->custid == -2 ? ' <i style="color: red">[ '.$row->bn_ar.' ]</i>' : '';

                $subtotal = $row->penerimaan - $row->potongan + $row->pembulatan + $row->other_cost;

                $tot_penerimaan += $row->penerimaan;
                $tot_potongan += $row->potongan;
                $tot_pembulatan += $row->pembulatan;
                $tot_other_cost += $row->other_cost;
                $tot_all += $subtotal;
            @endphp

            <tr>
                <td align="center">{{ $no++ }}</td>
                <td align="center">{!! $row->nama_customer !!}</td>
                <td align="center">{{ $row->bank_nama }}</td>
                <td align="center">{{ GetCaraBayar($row->cara_terima) }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->paydate) }}</td>
                <td align="center">{{ $row->paycode }}</td>
                <td>{{ $row->keterangan }}</td>
                <td align="right">{{ format_uang($row->penerimaan, 2) }}</td>
                <td align="right">{{ format_uang($row->potongan, 2)}}</td>
                <td align="right">{{ format_uang($row->pembulatan, 2)}}</td>
                <td align="right">{{ format_uang($row->other_cost, 2)}}</td>
                <td align="right">{{ format_uang($subtotal, 2)}}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        @if ($hasData)
            <tr>
                <td colspan="7" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ format_uang($tot_penerimaan, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_potongan, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_pembulatan, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_other_cost, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_all, 2) }}</b></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection