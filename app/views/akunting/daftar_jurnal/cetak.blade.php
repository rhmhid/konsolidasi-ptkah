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
    <span class="rgt">
        <span class="til">Doc No.</span>
        <span class="num">{{ $data_db->gldoc }}</span>
    </span>

    PRINTOUT JOURNAL
    <span>Doc Date : {{ dbtstamp2stringina($data_db->gldate) }}</span>
</h2>

<table width="100%" class="pad">
    <tbody>
        <tr>
            <td width="10%;">Entry Date</td>
            <td width="15%;">: {{ $data_db->gldoc }}</td>
            <td width="10%;">Doc Type</td>
            <td width="15%;">: {{ $data_db->journal_name }}</td>
        </tr>
        <tr>
            <td>Entry Date</td>
            <td>: {{ dbtstamp2stringina($data_db->create_time) }}</td>
            <td>Doc Date</td>
            <td>: {{ dbtstamp2stringina($data_db->gldate) }}</td>
        </tr>
        <tr>
            <td>Posting Status</td>
            <td>: {{ $data_db->posted }}</td>
            <td>Posted By</td>
            <td>: {{ $data_db->posted_by }}</td>
        </tr>
        <tr>
            <td>Ref. Code</td>
            <td>: {{ $data_db->reff_code }}</td>
            <td>Supplier / Customer Name</td>
            <td>: {{ $data_db->supp_cust }}</td>
        </tr>
        <tr>
            <td>Short Text</td>
            <td colspan="3">: {{ $data_db->gldesc }}</td>
        </tr>
    </tbody>
</table>

<table width="100%" class="bdr1 pad">
    <thead>
        <tr>
            <th rowspan="2">GL Account</th>
            <th rowspan="2">Description</th>
            <th colspan="2">Amount</th>
            <th rowspan="2">Cost Center</th>
        </tr>
        <tr>
            <th>Debet</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rsd as $row)
            @php
                $row = FieldsToObject($row);

                $tot_deb += $row->debet;
                $tot_cre += $row->credit;
            @endphp

            <tr>
                <td align="center">{{ $row->coacode }}</td>
                <td>{{ $row->notes }}<br /><I>[ {{ $row->coaname }} ]</I></td>
                <td align="right">Rp. {{ format_uang($row->debet, 2) }}</td>
                <td align="right">Rp. {{ format_uang($row->credit, 2) }}</td>
                <td>{{ $row->cost_center }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" align="right"><b>TOTAL</b></td>
            <td align="right"><b>Rp. {{ format_uang($tot_deb, 2) }}</b></td>
            <td align="right"><b>Rp. {{ format_uang($tot_cre, 2) }}</b></td>
            <td><b>&nbsp;</b></td>
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