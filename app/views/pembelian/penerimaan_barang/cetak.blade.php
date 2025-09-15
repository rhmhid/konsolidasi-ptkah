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
        <span class="til">Kode Penerimaan</span><span class="num">{{ $data_db->grcode }}</span>
    </span>

    TANDA TERIMA BARANG
    <span>Tanggal Terima : {{ dbtstamp2stringlong_ina($data_db->grdate) }}</span>
</h2>

@php
    $asal_brg = $data_db->asal_brg == 1 ? 'PO' : 'Tanpa PO';
    $cara_beli = $data_db->cara_beli == 1 ? 'Cash' : 'Credit';
    $no = 0;
@endphp

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%">No Faktur / Tgl Faktur</td>
            <td width="20%">: {{ $data_db->no_faktur }} / {{ dbtstamp2stringina($data_db->tgl_faktur) }}</td>
            <td width="10%">Asal Barang</td>
            <td width="20%">: {{ $asal_brg }}</td>
        </tr>
        <tr>
            <td>Gudang Penerima</td>
            <td>: {{ $data_db->nama_gudang }}</td>
            <td>Kode PO</td>
            <td>: {{ $data_db->pocode }}</td>
        </tr>
        <tr>
            <td>Nama Supplier</td>
            <td>: {{ $data_db->nama_supp }}</td>
            <td>Cara Bayar</td>
            <td>: {{ $cara_beli }}</td>
        </tr>
        <tr>
            <td>Tanggal Jatuh Tempo</td>
            <td>: {{ dbtstamp2stringina($data_db->duedate) }}</td>
            <td>Keterangan</td>
            <td>: {{ $data_db->keterangan }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Barang</th>
            <th width="10%">Jumlah</th>
            <th width="5%">Harga (Rp.)</th>
            <th width="5%">Disc (%)</th>
            <th width="5%">Disc. @ (Rp)</th>
            <th width="5%">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $no++;
                $jumlah = floatval($row->vol).' '.$row->nama_satuan;
                $total += $row->subtotal;
		$diskonrp = $row->diskon_rp;
		$ongkir   = $row->ongkir;
		$materai  = $row->materai;
		$ppn 	  = $row->ppn_rp;
		$totalall = $row->totalall;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td>{{ $row->barang }}</td>
                <td align="center">{{ $jumlah }}</td>
                <td align="right">Rp. {{ format_uang($row->harga, 2) }}</td>
                <td align="center">{{ floatval($row->disc) }} %</td>
                <td align="right">Rp. {{ format_uang($row->disc_rp, 2) }}</td>
                <td align="right">Rp. {{ format_uang($row->subtotal, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="6" align="right"><b>Diskon</b></td>
            <td align="right"><b>Rp. {{ format_uang($diskon_rp, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Total + Diskon</b></td>
            <td align="right"><b>Rp. {{ format_uang($total, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Ongkos Kirim</b></td>
            <td align="right"><b>Rp. {{ format_uang($ongkir, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Materai</b></td>
            <td align="right"><b>Rp. {{ format_uang($materai, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>PPn</b></td>
            <td align="right"><b>Rp. {{ format_uang($ppn, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Other Cost</b></td>
            <td align="right"><b>Rp. {{ format_uang($materai, 2) }}</b></td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Total</b></td>
            <td align="right"><b>Rp. {{ format_uang($totalall, 2) }}</b></td>
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
