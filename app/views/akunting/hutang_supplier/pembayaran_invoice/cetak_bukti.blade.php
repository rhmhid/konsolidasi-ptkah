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
        <span class="til">Nomor</span><span class="num">{{ $data_db->paycode }}</span>
    </span>

    VOUCHER PEMBAYARAN KAS / BANK
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->paydate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Nama Supplier</td>
            <td>: {{ $data_db->nama_supp }}</td>
            <td width="10%">Petugas</td>
            <td>: {{ $data_db->petugas }}</td>
        </tr>
        <tr>
            <td>Cara Bayar</td>
            <td>: {{ GetCaraBayar($data_db->cara_bayar) }}</td>
            <td>Keterangan</td>
            <td>: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th>No. AP</th>
            <th>No. Invoice</th>
            <th>No. Faktur</th>
            <th>Keterangan</th>
            <th>Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $totInv += floatval($row->nominal_payment);
            @endphp

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->apdate) }}</td>
                <td align="center">{{ dbtstamp2stringina($row->duedate) }}</td>
                <td align="center">{{ $row->no_invoice }}</td>
                <td>{{ $row->ket_ap }}</td>
                <td align="right">Rp. {{ format_uang($row->nominal_payment, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($totInv, 2) }}</b></td>
        </tr>
    </tbody>
</table>

@if (!$rs_addless->EOF)
<br />
<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th colspan="5" align="left">ADD/LESS</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="15%">C.O.A</th>
            <th width="10%">Keterangan</th>
            <th width="5%">Debet</th>
            <th width="5%">Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs_addless as $rec)
            @php
                $rec = FieldsToObject($rec);

                $totDr += floatval($rec->debet);
                $totCr += floatval($rec->credit);
            @endphp

            <tr>
                <td align="center">{{ ++$no_addless }}</td>
                <td>{{ $rec->coa }}</td>
                <td>{{ $rec->ket_addless }}</td>
                <td align="right">Rp. {{ format_uang($rec->debet, 2) }}</td>
                <td align="right">Rp. {{ format_uang($rec->credit, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" align="right"><b>SUBTOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($totDr, 2) }}</b></td>
            <td align="right"><b>Rp. {{ format_uang($totCr, 2) }}</b></td>
        </tr>
    </tbody>
</table>
@endif

<br />
<table width="100%" class="bdr1 pad sml">
    <tfoot>
        <tr>
            <td align="right" width="75%"><b>POTONGAN</b></td>
            <td align="right"><b>Rp. {{ format_uang($data_db->potongan, 2) }}</b></td>
        </tr>
        <tr>
            <td align="right"><b>PEMBULATAN</b></td>
            <td align="right"><b>Rp. {{ format_uang($data_db->pembulatan, 2) }}</b></td>
        </tr>
        <tr>
            <td align="right"><b>OTHER COST</b></td>
            <td align="right"><b>Rp. {{ format_uang($data_db->other_cost, 2) }}</b></td>
        </tr>
        <tr>
            <td align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($data_db->totpay, 2) }}</b></td>
        </tr>
        <tr>
            <td align="center" colspan="2"><b># {{ strtoupper(terbilang(floatval($data_db->totpay))) }} RUPIAH #</b></td>
        </tr>
    </tfoot>
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