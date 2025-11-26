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
        LAPORAN POSISI KEUANGAN<br />
        UNTUK PERIODE YANG BERAKHIR {{ $sdate }} DAN {{ $edate }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<div id="ftr" class="tp2">
    <div class="wpr">
        <table width="99%" class="pad">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>{{ $bln }}</th>
                    <th>{{ $bln_prev }}</th>
                </tr>
            </thead>
            <tbody>
                @if (!$empty_aktiva)
                    @foreach ($data_pos[1] as $row)
                        @foreach ($row as $rec)
                            @php
                                $rec = FieldsToObject($rec);
                            @endphp

                            <tr style="background: {{ $rec->color }};">
                                <td>{!! $rec->nama_pos !!}</td>
                                <td align="right">{!! $rec->amount !!}</td>
                                <td align="right">{!! $rec->amount_prev !!}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif

                @if (!$without_mapping)
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr style="background: #F2ECEC">
                    <td><b>POS NERACA LAINNYA</b></td>
                    <td align="right"><b><u>{!! $pos_amount !!}</u></b></td>
                    <td align="right"><b><u>{!! $pos_amount_prev !!}</u></b></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="wpr">
        <table width="99%" class="pad">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>{{ $bln }}</th>
                    <th>{{ $bln_prev }}</th>
                </tr>
            </thead>
            <tbody>
                @if (!$empty_pasive)
                    @foreach ($data_pos[2] as $row)
                        @foreach ($row as $rec)
                            @php
                                $rec = FieldsToObject($rec);
                            @endphp

                            <tr style="background: {{ $rec->color }};">
                                <td>{!! $rec->nama_pos !!}</td>
                                <td align="right">{!! $rec->amount !!}</td>
                                <td align="right">{!! $rec->amount_prev !!}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection