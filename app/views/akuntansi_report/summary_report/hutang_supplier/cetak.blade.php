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
    Summary Report A/P Purchasing
    <span style="text-transform: uppercase;">Cabang : {{ $cabang }}</span>
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

@php
    $jml_bid = 1;
@endphp
<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Cabang</th>
            <th rowspan="2">Begining Balance Total</th>
            <th colspan="5">A/P PURCHASING INVOICE</th>
            <th colspan="5">A/P PURCHASING PAYMENT</th>
            <th rowspan="2">Other Jurnal</th>
            <th rowspan="2">Ending Balance Total</th>
        </tr>
        <tr>
            <th>Begining</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>End</th>
            <th>Begining</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Balance</th>
            <th>End</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@endsection