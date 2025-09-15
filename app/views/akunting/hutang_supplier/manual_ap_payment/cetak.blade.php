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

    INVOICE PEMBAYARAN KAS / BANK
    <span>Tanggal : {{ dbtstamp2stringlong_ina($data_db->paydate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">Nama Supplier/Sub Dokter</td>
            <td width="20%">: {{ $data_db->nama_supp }} {!! $nama_dokter !!}</td>
            <td width="10%">Petugas</td>
            <td width="20%">: {{ $data_db->petugas }}</td>
        </tr>
        <tr>
            <td>Kas / Bank</td>
            <td>: {{ $data_db->bank_nama }}</td>
            <td>No. Pembayaran</td>
            <td>: {{ $data_db->no_bayar }}</td>
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
            <th>No.</th>
            <th>No. A/P</th>
            <th>No. Invoice</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                ++$no;
                $subtotal += $row->nominal_hutang;
                $potongan = $row->potongan;
                $pembulatan = $row->pembulatan;
                $other_cost = $row->other_cost;
                $tax_doctor = $row->tax_doctor;
                $total = floatval($subtotal) - floatval($potongan) + floatval($pembulatan) + floatval($other_cost) - floatval($tax_doctor);
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td>{{ $row->apcode }}</td>
                <td>{{ $row->no_inv }}</td>
                <td align="right">Rp. {{ format_uang($row->nominal_hutang, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" align="right"><b>SUBTOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($subtotal, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>POTONGAN</b></td>
            <td align="right"><b>Rp. {{ format_uang($potongan, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>PEMBULATAN</b></td>
            <td align="right"><b>Rp. {{ format_uang($pembulatan, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="3" align="right"><b>BIAYA LAINNYA</b></td>
            <td align="right"><b>Rp. {{ format_uang($other_cost, 2) }}</b></td>
        </tr>

        @if ($data_db->suppid == -1)
            <tr>
                <td colspan="3" align="right"><b>PAJAK DOKTER</b></td>
                <td align="right"><b>Rp. {{ format_uang($tax_doctor, 2) }}</b></td>
            </tr>
        @endif

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