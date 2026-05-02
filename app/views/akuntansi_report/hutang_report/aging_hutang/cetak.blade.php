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

@push('script')
<script type="text/javascript">
    function DetailAging (bid)
    {
        let $param = 'bid=' + bid
            $param += '&month={{ $data['month'] }}'
            $param += '&year={{ $data['year'] }}'
            $param += '&status_cabang={{ $data['status_cabang'] }}'

        let $link = "{{ route('hutang_report.aging_hutang.detail') }}"

        popFullScreen2($link + '?' + $param)
        return false
    }
</script>
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
    <span style="text-transform: uppercase;">Cabang : {{ $cabang }}</span>
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Cabang</th>
            <th rowspan="2">Saldo</th>
            <th colspan="5">Umur Pembayaran</th>
        </tr>
        <tr>
            <th><= 0</th>
            <th>0 - 30</th>
            <th>30 - 60</th>
            <th>60 - 90</th>
            <th>> 90</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $hasData = false;

            $totall = $totup0 = $totup1 = $totup2 = $totup3 = $totup4 = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $hasData = true;
                $row = FieldsToObject($row);

                $totall += $row->saldo;
                $totup0 += floatval($row->up0);
                $totup1 += floatval($row->up1);
                $totup2 += floatval($row->up2);
                $totup3 += floatval($row->up3);
                $totup4 += floatval($row->up4);
            @endphp

            <tr>
                <td align="center">{{ $no++ }}</td>
                <td>
                    <a href="javascript:void(0)" onclick="DetailAging('{{ $row->bid }}');">{{ $row->branch_name }}</a>
                </td>
                <td align="right">{{ format_uang($row->saldo, 2) }}</td>
                <td align="right">{{ format_uang($row->up0, 2) }}</td>
                <td align="right">{{ format_uang($row->up1, 2) }}</td>
                <td align="right">{{ format_uang($row->up2, 2) }}</td>
                <td align="right">{{ format_uang($row->up3, 2) }}</td>
                <td align="right">{{ format_uang($row->up4, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        @if ($hasData)
            <tr>
                <td colspan="2" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ $totall > 0 ? format_uang($totall, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup0 > 0 ? format_uang($totup0, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup1 > 0 ? format_uang($totup1, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup2 > 0 ? format_uang($totup2, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup3 > 0 ? format_uang($totup3, 2) : '' }}</b></td>
                <td align="right"><b>{{ $totup4 > 0 ? format_uang($totup4, 2) : '' }}</b></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection