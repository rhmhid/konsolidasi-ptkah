@extends('layouts.print')

@push('css')
<style type="text/css">
    table tr th { vertical-align: middle; text-align: center; }
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
    Profit And Loss
    <span style="text-transform: uppercase;">Cabang : {{ $cabang }}</span>
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Coacode</th>
            <th rowspan="2">Coaname</th>
            <th rowspan="2">Dr / Cr Position</th>

            @foreach ($data_cabang as $bc => $cbg)
                <th colspan="3">{{ $cbg['branch_name'] }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th colspan="3">Total All Branch</th>
            @endif
        </tr>
        <tr>
            @foreach ($data_cabang as $bc => $cbg)
                <th>{{ $bln }}</th>
                <th>{{ $bln_prev }}</th>
                <th>Until {{ $bln }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th>{{ $bln }}</th>
                <th>{{ $bln_prev }}</th>
                <th>Until {{ $bln }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data_pl as $coatid => $group)
            @php
                $nomor = 1;
            @endphp

            @foreach ($group['data'] as $coaid => $row)
                <tr>
                    <td align="center">{{ $nomor++ }}</td>
                    <td align="center">{{ $row['coacode'] }}</td>
                    <td>{{ $row['coaname'] }}</td>
                    <td align="center">{{ $row['posisi'] }}</td>

                    @foreach ($data_cabang as $bc => $cbg)
                        <td align="right">{{ format_uang($row['branches'][$bc]['amount_bln'] ?? 0, 2) }}</td>
                        <td align="right">{{ format_uang($row['branches'][$bc]['amount_bln_prev'] ?? 0, 2) }}</td>
                        <td align="right">{{ format_uang($row['branches'][$bc]['closingbal'] ?? 0, 2) }}</td>
                    @endforeach

                    @if(count($data_cabang) > 1)
                        <td align="right">{{ format_uang($row['total']['amount_bln'], 2) }}</td>
                        <td align="right">{{ format_uang($row['total']['amount_bln_prev'], 2) }}</td>
                        <td align="right">{{ format_uang($row['total']['closingbal'], 2) }}</td>
                    @endif
                </tr>
            @endforeach

            <tr>
                <td colspan="4"><b>SUBTOTAL {{ $group['headname'] }}</b></td>

                @foreach ($data_cabang as $bc => $cbg)
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['branches'][$bc]['amount_bln'] ?? 0, 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['branches'][$bc]['amount_bln_prev'] ?? 0, 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['branches'][$bc]['closingbal'] ?? 0, 2) }}</b></td>
                @endforeach

                @if(count($data_cabang) > 1)
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['total']['amount_bln'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['total']['amount_bln_prev'], 2) }}</b></td>
                    <td align="right"><b>{{ format_uang($subtotals[$coatid]['total']['closingbal'], 2) }}</b></td>
                @endif
            </tr>
            <tr>
                @php
                    $colspan_kosong = 4 + (count($data_cabang) * 3) + (count($data_cabang) > 1 ? 3 : 0);
                @endphp

                <td colspan="{{ $colspan_kosong }}"><b>&nbsp;</b></td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4"><b>TOTAL LABA / RUGI</b></td>

            @foreach ($data_cabang as $bc => $cbg)
                <td align="right"><b>{{ format_uang($laba_rugi['branches'][$bc]['amount_bln'] ?? 0, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($laba_rugi['branches'][$bc]['amount_bln_prev'] ?? 0, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($laba_rugi['branches'][$bc]['closingbal'] ?? 0, 2) }}</b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td align="right"><b>{{ format_uang($laba_rugi['total']['amount_bln'], 2) }}</b></td>
                <td align="right"><b>{{ format_uang($laba_rugi['total']['amount_bln_prev'], 2) }}</b></td>
                <td align="right"><b>{{ format_uang($laba_rugi['total']['closingbal'], 2) }}</b></td>
            @endif
        </tr>
    </tbody>
</table>
@endsection