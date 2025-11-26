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
    <span style="text-transform: uppercase;">Accounting Periode : {{ $period }}</span>
    <span style="text-transform: uppercase;">Report Month : {{ $report_month }}</span>
</h2>

<div id="ftr" class="tp2">
    <div class="wpr">
        <table width="99%" class="bdr2 pad">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Coacode</th>
                    <th>Coaname</th>
                    <th>Dr / Cr Position</th>
                    <th>Openning Balance</th>
                    <th>Closing Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5"><b>ASSET</b></td>
                </tr>

                @if (!$empty_asset)
                    @foreach ($data_bs[1] as $row)
                        @php
                            $row = FieldsToObject($row);
                        @endphp

                        <tr>
                            <td align="center">{{ $row->nomor }}</td>
                            <td align="center">{{ $row->coacode }}</td>
                            <td>{{ $row->coaname }}</td>
                            <td align="center">{{ $row->posisi }}</td>
                            <td align="right">{{ format_uang($row->opbal, 2) }}</td>
                            <td align="right">{{ format_uang($row->closbal, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="4"><b>TOTAL ASSET</b></td>
                    <td align="right"><b>{{ format_uang($tot_asset, 2) }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="wpr">
        <table width="99%" class="bdr2 pad">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Coacode</th>
                    <th>Coaname</th>
                    <th>Dr / Cr Position</th>
                    <th>Openning Balance</th>
                    <th>Closing Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5"><b>LIABILITY</b></td>
                </tr>

                @if (!$empty_libility)
                    @foreach ($data_bs[2] as $row)
                        @php
                            $row = FieldsToObject($row);
                        @endphp

                        <tr>
                            <td align="center">{{ $row->nomor }}</td>
                            <td align="center">{{ $row->coacode }}</td>
                            <td>{{ $row->coaname }}</td>
                            <td align="center">{{ $row->posisi }}</td>
                            <td align="right">{{ format_uang($row->opbal, 2) }}</td>
                            <td align="right">{{ format_uang($row->closbal, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="4"><b>TOTAL LIABILITY</b></td>
                    <td align="right"><b>{{ format_uang($tot_libility, 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5"><b>EQUITY</b></td>
                </tr>
                
                @if (!$empty_equity)
                    @foreach ($data_bs[3] as $row)
                        @php
                            $row = FieldsToObject($row);
                        @endphp

                        <tr>
                            <td align="center">{{ $row->nomor }}</td>
                            <td align="center">{{ $row->coacode }}</td>
                            <td>{{ $row->coaname }}</td>
                            <td align="center">{{ $row->posisi }}</td>
                            <td align="right">{{ format_uang($row->opbal, 2) }}</td>
                            <td align="right">{{ format_uang($row->closbal, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <td>&nbsp;</td>
                    <td colspan="4"><b>TOTAL EQUITY</b></td>
                    <td align="right"><b>{{ format_uang($tot_equity, 2) }}</b></td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="4"><b>TOTAL LIABILITY + EQUITY</b></td>
                    <td align="right"><b>{{ format_uang($tot_libility_equity, 2) }}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection