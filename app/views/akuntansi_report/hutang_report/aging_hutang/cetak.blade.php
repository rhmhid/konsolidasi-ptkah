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
    Laporan Aging Hutang
    <span style="text-transform: uppercase;">Sampai Dengan : {{ $sdate }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Type Trans</th>
            <th rowspan="2">Nama Supplier</th>
            <th rowspan="2">No. Invoice</th>
            <th rowspan="2">Tgl Invoice</th>
            <th rowspan="2">Duedate</th>
            <th rowspan="2">Nominal</th>
            <th colspan="7">Umur Pembayaran</th>
        </tr>
        <tr>
            <th><= 0 (Current)</th>
            <th>1 - 7</th>
            <th>8 - 14</th>
            <th>15 - 30</th>
            <th>31 - 60</th>
            <th>61 - 90</th>
            <th>> 90</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $nama_supp = null;
            $hasData = false;

            $totall = $totup0 = $totup1 = $totup2 = $totup3 = $totup4 = $totup5 = $totup6 = 0;
            $subtotall = $subtotup0 = $subtotup1 = $subtotup2 = $subtotup3 = $subtotup4 = $subtotup5 = $subtotup6 = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $hasData = true;
                $row = FieldsToObject($row);

                $row->nama_supp .= $row->suppid == -1 ? ' <i style="color: red">[ '.$row->nama_dokter.' ]</i>' : '';

                if ($nama_supp !== null && $nama_supp !== $row->nama_supp) {
            @endphp
                <tr>
                    <td colspan="6" align="right"><b>SUBTOTAL {!! $nama_supp !!}</b></td>
                    <td align="right"><b>{{ $subtotall > 0 ? format_uang($subtotall, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup0 > 0 ? format_uang($subtotup0, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup1 > 0 ? format_uang($subtotup1, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup2 > 0 ? format_uang($subtotup2, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup3 > 0 ? format_uang($subtotup3, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup4 > 0 ? format_uang($subtotup4, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup5 > 0 ? format_uang($subtotup5, 2) : '' }}</b></td>
                    <td align="right"><b>{{ $subtotup6 > 0 ? format_uang($subtotup6, 2) : '' }}</b></td>
                </tr>
            @php
                    $subtotall = $subtotup0 = $subtotup1 = $subtotup2 = $subtotup3 = $subtotup4 = $subtotup5 = $subtotup6 = 0;
                }

                $up = $row->up == '00:00:00' ? 0 : trim($row->up, ' days');
                $up = intval($up);

                $up0 = $up1 = $up2 = $up3 = $up4 = $up5 = $up6 = '';
                if ($up <= 0) $up0 = $row->nominal;
                elseif ($up < 8) $up1 = $row->nominal;
                elseif ($up < 15) $up2 = $row->nominal;
                elseif ($up < 31) $up3 = $row->nominal;
                elseif ($up < 61) $up4 = $row->nominal;
                elseif ($up < 91) $up5 = $row->nominal;
                else $up6 = $row->nominal;

                $subtotall += $row->nominal;
                $subtotup0 += floatval($up0);
                $subtotup1 += floatval($up1);
                $subtotup2 += floatval($up2);
                $subtotup3 += floatval($up3);
                $subtotup4 += floatval($up4);
                $subtotup5 += floatval($up5);
                $subtotup6 += floatval($up6);

                $totall += $row->nominal;
                $totup0 += floatval($up0);
                $totup1 += floatval($up1);
                $totup2 += floatval($up2);
                $totup3 += floatval($up3);
                $totup4 += floatval($up4);
                $totup5 += floatval($up5);
                $totup6 += floatval($up6);

                $nama_supp = $row->nama_supp;
            @endphp

            <tr>
                <td align="center">{{ $no++ }}</td>
                <td align="center">{{ $row->journal_name }}</td>
                <td>{!! $row->nama_supp !!}</td>
                <td align="center">{{ $row->no_inv }}</td>
                <td align="center">{{ dbtstamp2stringlong_ina($row->apdate) }}</td>
                <td align="center">{{ dbtstamp2stringina($row->duedate) }}</td>
                <td align="right">{{ format_uang($row->nominal, 2) }}</td>
                <td align="right">{{ format_uang($up0, 2) }}</td>
                <td align="right">{{ format_uang($up1, 2) }}</td>
                <td align="right">{{ format_uang($up2, 2) }}</td>
                <td align="right">{{ format_uang($up3, 2) }}</td>
                <td align="right">{{ format_uang($up4, 2) }}</td>
                <td align="right">{{ format_uang($up5, 2) }}</td>
                <td align="right">{{ format_uang($up6, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        @if ($hasData)
            <tr>
                <td colspan="6" align="right"><b>SUBTOTAL {!! $nama_supp !!}</b></td>
                <td align="right"><b>{{ $subtotall > 0 ? format_uang($subtotall, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup0 > 0 ? format_uang($subtotup0, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup1 > 0 ? format_uang($subtotup1, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup2 > 0 ? format_uang($subtotup2, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup3 > 0 ? format_uang($subtotup3, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup4 > 0 ? format_uang($subtotup4, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup5 > 0 ? format_uang($subtotup5, 2) : '' }}</b></td>
                <td align="right"><b>{{ $subtotup6 > 0 ? format_uang($subtotup6, 2) : '' }}</b></td>
            </tr>
            <tr>
                <td colspan="6" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ $totall > 0 ? format_uang($totall, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup0 > 0 ? format_uang($totup0, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup1 > 0 ? format_uang($totup1, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup2 > 0 ? format_uang($totup2, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup3 > 0 ? format_uang($totup3, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup4 > 0 ? format_uang($totup4, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup5 > 0 ? format_uang($totup5, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup6 > 0 ? format_uang($totup6, 2) : '' }}</b></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection