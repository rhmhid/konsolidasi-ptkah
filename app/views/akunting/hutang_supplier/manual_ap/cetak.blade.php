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
        <span class="til">No. A/P</span><span class="num">{{ $data_db->apcode }}</span>
    </span>

    INVOICE PENGAJUAN PEMBAYARAN KAS / BANK
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->apdate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Petugas</td>
            <td width="20%">: {{ $data_db->petugas }}</td>
            <td width="10%">Nama Supplier/Sub Dokter</td>
            <td width="20%">: {{ $data_db->nama_supp }} {!! $nama_dokter !!}</td>
        </tr>
        <tr>
            <td>No. Invoice</td>
            <td>: {{ $data_db->no_inv }}</td>
            <td>Duedate</td>
            <td>: {{ dbtstamp2stringina($data_db->duedate) }}</td>
        </tr>
        <tr>
            <td>No. Faktur Pajak</td>
            <td>: {{ $data_db->faktur_pajak }}</td>
            <td>Tanggal Faktur Pajak</td>
            <td>: {{ dbtstamp2stringina($data_db->tgl_faktur_pajak) }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="3">: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th>No</th>
            <th>C.O.A</th>
            <th>Keterangan</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                ++$no;
                $coa = $row->coacode.' - '.$row->coaname;

                $subtotal += $row->amount;
                $ppn_rp = $row->ppn_rp;
                $total = floatval($subtotal + $ppn_rp);
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td>{{ $coa }}</td>
                <td>{{ $row->detailnote }}</td>
                <td align="right">Rp. {{ format_uang($row->amount, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" align="right"><b>SUBTOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($subtotal, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>PPn</b></td>
            <td align="right"><b>Rp. {{ format_uang($ppn_rp, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($total, 2) }}</b></td>
        </tr>
        <tr>
            <td align="center" colspan="4"><b># {{ strtoupper(terbilang($total)) }} RUPIAH #</b></td>
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