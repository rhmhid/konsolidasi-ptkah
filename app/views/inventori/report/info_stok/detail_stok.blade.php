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
    function DetailStock (mbid = 0)
    {
        let $param = 'sdate={{ $data['sdate'] }}'
            $param += '&mbid=' + mbid

        let $link = "{{ route('inventori_report.info_stok.detail_stok') }}"

        NewWindow($link + '?' + $param, 'Detail Stok Per Gudang', 600, 500, 'yes')
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

@section('content')
<h2 class="bdr">
    DETAIL STOK {{ $nama_brg }}
    <span style="text-transform: uppercase;">Sampai Dengan Periode {{ $periode }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Gudang</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $tot_stock += $row->stock;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td>{{ $row->nama_gudang }}</td>
                <td align="right">{{ floatval($row->stock) }}</td>
            </tr>
        @endforeach

        <tr>
            <td align="right" colspan="2"><b>TOTAL STOK</b></td>
            <td align="right"><b>{{ floatval($tot_stock) }}</b></td>
        </tr>
    </tbody>
</table>
@endsection