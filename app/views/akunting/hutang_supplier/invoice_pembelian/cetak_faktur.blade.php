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
<h2 class="bdr ctr">Tanda Terima Faktur</h2>

<table width="100%" class="bdr1 pad sml">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Penerimaan/PO</th>
            <th>No. Invoice</th>
            <th>Supplier</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $po_gr = $row->grcode;
                if ($row->pocode != '') $po_gr .= ' / '.$row->pocode;

                $total += floatval($row->amount);
            @endphp

            <tr>
                <td align="center">{{ ++$no }}</td>
                <td align="center">{{ $po_gr }}</td>
                <td align="center">{{ $row->no_invoice }}</td>
                <td align="center">{{ $row->nama_supp }}</td>
                <td align="right">Rp. {{ format_uang($row->amount, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($total, 2) }}</b></td>
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
                Diterima Oleh
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
                Diserahkan Oleh
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