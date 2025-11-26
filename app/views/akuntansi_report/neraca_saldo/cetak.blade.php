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
    function BukuBesar (coaid)
    {
        let $param = 'sdate={{ $data['sdate'] }}'
            $param += '&edate={{ $data['edate'] }}'
            $param += '&coaid_from=' + coaid
            $param += '&coaid_to=' + coaid
            $param += '&with_bb=t'
            $param += '&coa_vs=t'

        let $link = "{{ route('akuntansi_report.buku_besar.cetak') }}"

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
    Trial Balance
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Coacode</th>
            <th>Coaname</th>
            <th>Dr / Cr Position</th>
            <th>Beginning Balance</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Ending Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data_tb as $row)
            @php
                $row = FieldsToObject($row);
            @endphp

            <tr>
                <td align="center">{{ $row->no }}</td>
                <td align="center">{{ $row->coacode }}</td>
                <td>
                    <a href="javascript:void(0)" onclick="BukuBesar('{{ $row->coaid }}');">{{ $row->coaname }}</a>
                </td>
                <td align="center">{{ $row->posisi }}</td>
                <td align="right">{{ format_uang($row->opening, 2) }}</td>
                <td align="right">{{ format_uang($row->debet, 2) }}</td>
                <td align="right">{{ format_uang($row->credit, 2) }}</td>
                <td align="right">{{ format_uang($row->balance, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="4" align="right"><b>SUBTOTAL</b></td>
            <td align="right"><b></b></td>
            <td align="right"><b>{{ format_uang($tot_deb, 2) }}</b></td>
            <td align="right"><b>{{ format_uang($tot_cre, 2) }}</b></td>
            <td align="right"><b></b></td>
        </tr>
    </tbody>
</table>
@endsection