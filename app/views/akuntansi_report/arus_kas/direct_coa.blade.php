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

@section('content')
<h2 class="bdr ctr">
    <p style="line-height: 1.2em;">
        @if (isMultiTenants() == 't')
            {{ Auth::user()->branch->branch_name }}
        @else
            {{ dataConfigs('company_name') }}
        @endif<br />
        DETAIL COA CASHFLOW {{ $subtitle }}<br />
        PER {{ strtoupper($periode) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
    <span style="text-transform: uppercase;">Pos : {{ $cf_name }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>Tanggal Transaksi</th>
            <th>C.O.A</th>
            <th>No. Doc</th>
            <th>Type Transaksi</th>
            <th>Description</th>
            <th>Notes</th>
            <th>Amount</th>
            <th>user Entry</th>
        </tr>
    </thead>
    <tbody>
        @php
            $tot_amount = 0;
            $subtot_amount = 0;
            $current_branch = null;
        @endphp

        @foreach ($rs as $row)
            @php
                $row = FieldsToObject($row);
            @endphp

            {{-- Pengecekan jika nama cabang berganti --}}
            @if ($current_branch !== $row->branch_name)
                
                {{-- Cetak Subtotal cabang sebelumnya (kecuali pada looping pertama) --}}
                @if ($current_branch !== null)
                    <tr style="background-color: #f9f9f9;">
                        <td align="right" colspan="6"><b>SUBTOTAL {{ strtoupper($current_branch) }}</b></td>
                        <td align="right"><b>{{ format_uang($subtot_amount, 2) }}</b></td>
                        <td></td>
                    </tr>
                @endif

                {{-- Update nama cabang yang sedang aktif & reset subtotal --}}
                @php
                    $current_branch = $row->branch_name;
                    $subtot_amount = 0;
                @endphp

                {{-- Cetak Header Cabang baru --}}
                <tr style="background-color: #e0e0e0;">
                    <td colspan="8" align="left"><b>CABANG: {{ strtoupper($current_branch) }}</b></td>
                </tr>
            @endif

            @php
                // Tambahkan amount ke subtotal & total
                $subtot_amount += $row->amount;
                $tot_amount += $row->amount;
            @endphp

            <tr>
                <td align="center">{{ dbtstamp2stringlong_ina($row->gldate) }}</td>
                <td>{{ $row->coacode.' '.$row->coaname }}</td>
                <td align="center">{{ $row->gldoc }}</td>
                <td align="center">{{ $row->gltype }}</td>
                <td>{{ $row->gldesc }}</td>
                <td>{{ $row->glnotes }}</td>
                <td align="right">{{ format_uang($row->amount, 2) }}</td>
                <td>{{ $row->gluser }}</td>
            </tr>
        @endforeach

        {{-- Jangan lupa cetak Subtotal untuk cabang yang terakhir kali dilooping --}}
        @if ($current_branch !== null)
            <tr style="background-color: #f9f9f9;">
                <td align="right" colspan="6"><b>SUBTOTAL {{ strtoupper($current_branch) }}</b></td>
                <td align="right"><b>{{ format_uang($subtot_amount, 2) }}</b></td>
                <td></td>
            </tr>
        @endif

        <tr style="background-color: #d9d9d9;">
            <td align="right" colspan="6"><b>TOTAL KESELURUHAN</b></td>
            <td align="right"><b>{{ format_uang($tot_amount, 2) }}</b></td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection