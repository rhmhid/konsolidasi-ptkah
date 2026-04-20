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
    function DetailSummary (bid)
    {
        let $param = 'bid=' + bid
            $param += '&month={{ $data['month'] }}'
            $param += '&year={{ $data['year'] }}'

        let $link = "{{ route('summary_report.ap_dokter.detail') }}"

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
    Summary Report A/P Doctor
    <span style="text-transform: uppercase;">Cabang : {{ $cabang }}</span>
    <span style="text-transform: uppercase;">Periode : {{ $report_month }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Cabang</th>
            <th>Begining Balance Total</th>
            <th>A/P Doctor Invoice</th>
            <th>A/P Doctor Payment</th>
            <th>Ending Balance</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $tot_opbal = $tot_ap_inv = $tot_ap_pay = $tot_closbal = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $row = FieldsToObject($row);

                $tot_opbal += $row->opbal ?? 0;
                $tot_ap_inv += $row->ap_inv ?? 0;
                $tot_ap_pay += $row->ap_pay ?? 0;
                $tot_closbal += $row->closbal ?? 0;
            @endphp

            <tr>
                <td align="center">{{ $no++ }}</td>
                <td>
                    <a href="javascript:void(0)" onclick="DetailSummary('{{ $row->bid }}');">{{ $row->branch_name }}</a></td>
                <td align="right">{{ format_uang($row->opbal, 2) }}</td>
                <td align="right">{{ format_uang($row->ap_inv, 2) }}</td>
                <td align="right">{{ format_uang($row->ap_pay, 2) }}</td>
                <td align="right">{{ format_uang($row->closbal, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse
    </tbody>
    @if ($no > 1)
        <tfoot>
            <tr>
                <td colspan="2" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ format_uang($tot_opbal, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_ap_inv, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_ap_pay, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_closbal, 2) }}</b></td>
            </tr>
        </tfoot>
    @endif
</table>
@endsection