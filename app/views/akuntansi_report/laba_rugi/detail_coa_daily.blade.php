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

@section('content')
<h2 class="bdr ctr">
    <p style="line-height: 1.2em;">
        @if (isMultiTenants() == 't')
            {{ Auth::user()->branch->branch_name }}
        @else
            {{ dataConfigs('company_name') }}
        @endif<br />
        LAPORAN RUGI / LABA DAILY DETAIL COA <br />
        PER {{ strtoupper($sdate) }} DAN {{ strtoupper($edate) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="pad bdr2">
    <thead>
        <tr>
            <th rowspan="2">C.O.A</th>

            @foreach ($data_cabang as $bc => $cabang)
                <th colspan="2">{{ $cabang['branch_name'] }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th colspan="2">Total All Branch</th>
            @endif
        </tr>
        <tr>
            @foreach ($data_cabang as $bc => $cabang)
                <th>{{ $sdate }} sd {{ $edate }}</th>
                <th>Until {{ $edate }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th>{{ $sdate }} sd {{ $edate }}</th>
                <th>Until {{ $edate }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $grand_totals = [
                'branches'  => [],
                'total'     => [
                    'amount_period' => 0,
                    'amount_untill' => 0
                ]
            ];
        @endphp

        @if (!$empty_pos)
            @foreach ($data_db as $coaid => $rec)
                <tr>
                    <td>{{ $rec['coa'] }}</td>

                    @foreach ($data_cabang as $bc => $cabang)
                        @php
                            $amt_per = $rec['branches'][$bc]['amount_period'] ?? 0;
                            $amt_unt = $rec['branches'][$bc]['amount_untill'] ?? 0;

                            $grand_totals['branches'][$bc]['amount_period'] = ($grand_totals['branches'][$bc]['amount_period'] ?? 0) + $amt_per;
                            $grand_totals['branches'][$bc]['amount_untill'] = ($grand_totals['branches'][$bc]['amount_untill'] ?? 0) + $amt_unt;
                        @endphp

                        <td align="right">{{ format_uang($amt_per, 2) }}</td>
                        <td align="right">{{ format_uang($amt_unt, 2) }}</td>
                    @endforeach
                    
                    @if(count($data_cabang) > 1)
                        @php
                            $grand_totals['total']['amount_period'] += $rec['total']['amount_period'];
                            $grand_totals['total']['amount_untill'] += $rec['total']['amount_untill'];
                        @endphp

                        <td align="right">{{ format_uang($rec['total']['amount_period'], 2) }}</td>
                        <td align="right">{{ format_uang($rec['total']['amount_untill'], 2) }}</td>
                    @endif
                </tr>
            @endforeach
        @endif

        <tr>
            <td align="right"><b>TOTAL</b></td>

            @foreach ($data_cabang as $bc => $cabang)
                <td align="right"><b>{{ format_uang($grand_totals['branches'][$bc]['amount_period'] ?? 0, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['branches'][$bc]['amount_untill'] ?? 0, 2) }}</b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td align="right"><b>{{ format_uang($grand_totals['total']['amount_period'], 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['total']['amount_untill'], 2) }}</b></td>
            @endif
        </tr>
    </tbody>
</table>
@endsection