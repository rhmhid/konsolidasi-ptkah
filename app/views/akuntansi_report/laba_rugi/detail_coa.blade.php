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
        LAPORAN RUGI / LABA DETAIL COA <br />
        PER {{ $sdate }} DAN {{ $edate }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="pad bdr2">
    <thead>
        <tr>
            <th rowspan="2">C.O.A</th>

            @foreach ($data_cabang as $bc => $cabang)
                <th colspan="3">{{ $cabang['branch_name'] }}</th>
            @endforeach

            @if(count($data_cabang) > 1)
                <th colspan="3">Total All Branch</th>
            @endif
        </tr>
        <tr>
            @foreach ($data_cabang as $bc => $cabang)
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
        @php
            $grand_totals = [
                'branches'  => [],
                'total'     => [
                    'amount_bln'        => 0,
                    'amount_bln_prev'   => 0,
                    'closingbal'        => 0
                ]
            ];
        @endphp

        @if (!$empty_pos)
            @foreach ($data_db as $coaid => $rec)
                <tr>
                    <td>{{ $rec['coa'] }}</td>

                    @foreach ($data_cabang as $bc => $cabang)
                        @php
                            $amt = $rec['branches'][$bc]['amount_bln'] ?? 0;
                            $amt_prev = $rec['branches'][$bc]['amount_bln_prev'] ?? 0;
                            $cls = $rec['branches'][$bc]['closingbal'] ?? 0;

                            $grand_totals['branches'][$bc]['amount_bln'] = ($grand_totals['branches'][$bc]['amount_bln'] ?? 0) + $amt;
                            $grand_totals['branches'][$bc]['amount_bln_prev'] = ($grand_totals['branches'][$bc]['amount_bln_prev'] ?? 0) + $amt_prev;
                            $grand_totals['branches'][$bc]['closingbal'] = ($grand_totals['branches'][$bc]['closingbal'] ?? 0) + $cls;
                        @endphp

                        <td align="right">{{ format_uang($amt, 2) }}</td>
                        <td align="right">{{ format_uang($amt_prev, 2) }}</td>
                        <td align="right">{{ format_uang($cls, 2) }}</td>
                    @endforeach

                    @if(count($data_cabang) > 1)
                        @php
                            $grand_totals['total']['amount_bln'] += $rec['total']['amount_bln'];
                            $grand_totals['total']['amount_bln_prev'] += $rec['total']['amount_bln_prev'];
                            $grand_totals['total']['closingbal'] += $rec['total']['closingbal'];
                        @endphp

                        <td align="right">{{ format_uang($rec['total']['amount_bln'], 2) }}</td>
                        <td align="right">{{ format_uang($rec['total']['amount_bln_prev'], 2) }}</td>
                        <td align="right">{{ format_uang($rec['total']['closingbal'], 2) }}</td>
                    @endif
                </tr>
            @endforeach
        @endif

        <tr>
            <td align="right"><b>TOTAL</b></td>

            @foreach ($data_cabang as $bc => $cabang)
                <td align="right"><b>{{ format_uang($grand_totals['branches'][$bc]['amount_bln'] ?? 0, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['branches'][$bc]['amount_bln_prev'] ?? 0, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['branches'][$bc]['closingbal'] ?? 0, 2) }}</b></td>
            @endforeach

            @if(count($data_cabang) > 1)
                <td align="right"><b>{{ format_uang($grand_totals['total']['amount_bln'], 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['total']['amount_bln_prev'], 2) }}</b></td>
                <td align="right"><b>{{ format_uang($grand_totals['total']['closingbal'], 2) }}</b></td>
            @endif
        </tr>
    </tbody>
</table>
@endsection