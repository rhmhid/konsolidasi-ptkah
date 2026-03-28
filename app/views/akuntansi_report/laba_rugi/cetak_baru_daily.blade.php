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
        LAPORAN REKAP RUGI / LABA DAILY<br />
        PER {{ strtoupper($sdate) }} DAN {{ strtoupper($edate) }}
    </p>

    <span style="text-transform: uppercase;">Tgl cetak : {{ strtoupper(dbtstamp2stringina($tgl_cetak)) }}</span>
</h2>

<table width="100%" class="pad">
    <thead>
        <tr>
            <th rowspan="2">Keterangan</th>
            
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
        @if (!$empty_pos)
            @foreach ($data_pos as $rec)
                @php
                    $rec = FieldsToObject($rec);
                    $amounts = json_decode(json_encode($rec->amounts), true);
                @endphp

                <tr>
                    <td>{!! $rec->nama_pos !!}</td>
                    
                    @foreach ($data_cabang as $bc => $cabang)
                        <td align="right">{!! $amounts['branches'][$bc]['amount_period'] ?? '' !!}</td>
                        <td align="right">{!! $amounts['branches'][$bc]['amount_untill'] ?? '' !!}</td>
                    @endforeach
                    
                    @if(count($data_cabang) > 1)
                        <td align="right">{!! $amounts['total']['amount_period'] !!}</td>
                        <td align="right">{!! $amounts['total']['amount_untill'] !!}</td>
                    @endif
                </tr>
            @endforeach
        @endif

        @if (!$without_mapping)
        <tr>
            @php
                $colspan_kosong = 1 + (count($data_cabang) * 2) + (count($data_cabang) > 1 ? 2 : 0);
            @endphp
            <td colspan="{{ $colspan_kosong }}">&nbsp;</td>
        </tr>
        <tr>
            <td><b>POS LABA/RUGI LAINNYA</b></td>
            @foreach ($data_cabang as $bc => $cabang)
                <td align="right"><b><u>{!! $pos_lainnya['branches'][$bc]['amount_period'] ?? '' !!}</u></b></td>
                <td align="right"><b><u>{!! $pos_lainnya['branches'][$bc]['amount_untill'] ?? '' !!}</u></b></td>
            @endforeach
            
            @if(count($data_cabang) > 1)
                <td align="right"><b><u>{!! $pos_lainnya['total']['amount_period'] !!}</u></b></td>
                <td align="right"><b><u>{!! $pos_lainnya['total']['amount_untill'] !!}</u></b></td>
            @endif
        </tr>
        @endif
    </tbody>
</table>
@endsection