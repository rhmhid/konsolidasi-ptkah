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
    Balance Sheet - Monthly
    <span style="text-transform: uppercase;">Cabang : {{ $cabang }}</span>
    <span style="text-transform: uppercase;">Accounting Periode : {{ $period }}</span>
    <span style="text-transform: uppercase;">Report Month : {{ $report_month }}</span>
</h2>

@php
    // Menghitung jumlah cabang untuk penentuan kolom Grand Total
    $jml_bid = 1;
    foreach ($data_cabang as $head) {
        $jml_bid++;
    }
@endphp

<div style="display: flex; align-items: flex-start; gap: 20px; width: 100%;">

    <div style="flex: 1; width: 50%; overflow-x: auto;">
        <table width="100%" class="bdr2 pad">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Coacode</th>
                    <th rowspan="2">Coaname</th>
                    <th rowspan="2">Dr/Cr</th>

                    @foreach ($data_cabang as $branch_code => $head)
                        <th colspan="2">{{ $head['branch_name'] }}</th>
                    @endforeach

                    @if ($jml_bid > 2)
                        <th colspan="2">Total All Branch</th>
                    @endif
                </tr>
                <tr>
                    @foreach ($data_cabang as $head)
                        <th>Op. Bal</th>
                        <th>Cl. Bal</th>
                    @endforeach

                    @if ($jml_bid > 2)
                        <th>Op. Bal</th>
                        <th>Cl. Bal</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>ASSET</b></td>
                    @foreach ($data_cabang as $head) <td colspan="2"></td> @endforeach
                    @if ($jml_bid > 2) <td colspan="2"></td> @endif
                </tr>

                @if (!$empty_asset && isset($data_bs[1]))
                    @foreach ($data_bs[1] as $coacode => $row)
                        @php
                            $row_tot_opbal = 0;
                            $row_tot_closbal = 0;
                        @endphp
                        <tr>
                            <td align="center">{{ $row['nomor'] }}</td>
                            <td align="center">{{ $row['coacode'] }}</td>
                            <td>{{ $row['coaname'] }}</td>
                            <td align="center">{{ $row['posisi'] }}</td>

                            @foreach ($data_cabang as $branch_code => $cab)
                                @php
                                    $opbal = $row['branch'][$branch_code]['opbal'];
                                    $closbal = $row['branch'][$branch_code]['closbal'];
                                    $row_tot_opbal += $opbal;
                                    $row_tot_closbal += $closbal;
                                @endphp
                                <td align="right">{{ format_uang($opbal, 2) }}</td>
                                <td align="right">{{ format_uang($closbal, 2) }}</td>
                            @endforeach

                            @if ($jml_bid > 2)
                                <td align="right"><b>{{ format_uang($row_tot_opbal, 2) }}</b></td>
                                <td align="right"><b>{{ format_uang($row_tot_closbal, 2) }}</b></td>
                            @endif
                        </tr>
                    @endforeach
                @endif

                <tr style="background-color: #f8f9fa;">
                    <td>&nbsp;</td>
                    <td colspan="3"><b>TOTAL ASSET</b></td>
                    
                    @php $grand_tot_asset = 0; @endphp
                    
                    @foreach ($data_cabang as $branch_code => $cab)
                        @php $grand_tot_asset += $tot_asset[$branch_code]; @endphp
                        <td></td> 
                        <td align="right"><b>{{ format_uang($tot_asset[$branch_code], 2) }}</b></td>
                    @endforeach

                    @if ($jml_bid > 2)
                        <td></td>
                        <td align="right"><b>{{ format_uang($grand_tot_asset, 2) }}</b></td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>

    <div style="flex: 1; width: 50%; overflow-x: auto;">
        <table width="100%" class="bdr2 pad">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Coacode</th>
                    <th rowspan="2">Coaname</th>
                    <th rowspan="2">Dr/Cr</th>

                    @foreach ($data_cabang as $branch_code => $head)
                        <th colspan="2">{{ $head['branch_name'] }}</th>
                    @endforeach

                    @if ($jml_bid > 2)
                        <th colspan="2">Total All Branch</th>
                    @endif
                </tr>
                <tr>
                    @foreach ($data_cabang as $head)
                        <th>Op. Bal</th>
                        <th>Cl. Bal</th>
                    @endforeach

                    @if ($jml_bid > 2)
                        <th>Op. Bal</th>
                        <th>Cl. Bal</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>LIABILITY</b></td>
                    @foreach ($data_cabang as $head) <td colspan="2"></td> @endforeach
                    @if ($jml_bid > 2) <td colspan="2"></td> @endif
                </tr>

                @if (!$empty_libility && isset($data_bs[2]))
                    @foreach ($data_bs[2] as $coacode => $row)
                        @php
                            $row_tot_opbal = 0;
                            $row_tot_closbal = 0;
                        @endphp
                        <tr>
                            <td align="center">{{ $row['nomor'] }}</td>
                            <td align="center">{{ $row['coacode'] }}</td>
                            <td>{{ $row['coaname'] }}</td>
                            <td align="center">{{ $row['posisi'] }}</td>

                            @foreach ($data_cabang as $branch_code => $cab)
                                @php
                                    $opbal = $row['branch'][$branch_code]['opbal'];
                                    $closbal = $row['branch'][$branch_code]['closbal'];
                                    $row_tot_opbal += $opbal;
                                    $row_tot_closbal += $closbal;
                                @endphp
                                <td align="right">{{ format_uang($opbal, 2) }}</td>
                                <td align="right">{{ format_uang($closbal, 2) }}</td>
                            @endforeach

                            @if ($jml_bid > 2)
                                <td align="right"><b>{{ format_uang($row_tot_opbal, 2) }}</b></td>
                                <td align="right"><b>{{ format_uang($row_tot_closbal, 2) }}</b></td>
                            @endif
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>TOTAL LIABILITY</b></td>
                    @php $grand_tot_libility = 0; @endphp
                    @foreach ($data_cabang as $branch_code => $cab)
                        @php $grand_tot_libility += $tot_libility[$branch_code]; @endphp
                        <td></td>
                        <td align="right"><b>{{ format_uang($tot_libility[$branch_code], 2) }}</b></td>
                    @endforeach
                    @if ($jml_bid > 2)
                        <td></td>
                        <td align="right"><b>{{ format_uang($grand_tot_libility, 2) }}</b></td>
                    @endif
                </tr>

                <tr><td colspan="{{ 4 + (($jml_bid-1)*2) + ($jml_bid > 2 ? 2 : 0) }}">&nbsp;</td></tr>

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>EQUITY</b></td>
                    @foreach ($data_cabang as $head) <td colspan="2"></td> @endforeach
                    @if ($jml_bid > 2) <td colspan="2"></td> @endif
                </tr>
                
                @if (!$empty_equity && isset($data_bs[3]))
                    @foreach ($data_bs[3] as $coacode => $row)
                        @php
                            $row_tot_opbal = 0;
                            $row_tot_closbal = 0;
                        @endphp
                        <tr>
                            <td align="center">{{ $row['nomor'] }}</td>
                            <td align="center">{{ $row['coacode'] }}</td>
                            <td>{{ $row['coaname'] }}</td>
                            <td align="center">{{ $row['posisi'] }}</td>

                            @foreach ($data_cabang as $branch_code => $cab)
                                @php
                                    $opbal = $row['branch'][$branch_code]['opbal'];
                                    $closbal = $row['branch'][$branch_code]['closbal'];
                                    $row_tot_opbal += $opbal;
                                    $row_tot_closbal += $closbal;
                                @endphp
                                <td align="right">{{ format_uang($opbal, 2) }}</td>
                                <td align="right">{{ format_uang($closbal, 2) }}</td>
                            @endforeach

                            @if ($jml_bid > 2)
                                <td align="right"><b>{{ format_uang($row_tot_opbal, 2) }}</b></td>
                                <td align="right"><b>{{ format_uang($row_tot_closbal, 2) }}</b></td>
                            @endif
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>TOTAL EQUITY</b></td>
                    @php $grand_tot_equity = 0; @endphp
                    @foreach ($data_cabang as $branch_code => $cab)
                        @php $grand_tot_equity += $tot_equity[$branch_code]; @endphp
                        <td></td>
                        <td align="right"><b>{{ format_uang($tot_equity[$branch_code], 2) }}</b></td>
                    @endforeach
                    @if ($jml_bid > 2)
                        <td></td>
                        <td align="right"><b>{{ format_uang($grand_tot_equity, 2) }}</b></td>
                    @endif
                </tr>

                <tr><td colspan="{{ 4 + (($jml_bid-1)*2) + ($jml_bid > 2 ? 2 : 0) }}">&nbsp;</td></tr>

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><b>TOTAL LIABILITY + EQUITY</b></td>
                    @php $grand_tot_libility_equity = 0; @endphp
                    @foreach ($data_cabang as $branch_code => $cab)
                        @php $grand_tot_libility_equity += $tot_libility_equity[$branch_code]; @endphp
                        <td></td>
                        <td align="right"><b>{{ format_uang($tot_libility_equity[$branch_code], 2) }}</b></td>
                    @endforeach
                    @if ($jml_bid > 2)
                        <td></td>
                        <td align="right"><b>{{ format_uang($grand_tot_libility_equity, 2) }}</b></td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>

</div>
@endsection