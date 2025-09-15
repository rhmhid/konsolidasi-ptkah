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
    Informasi Stok ( Stock Status )
    <span style="text-transform: uppercase;">Sampai Dengan Periode {{ $periode }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kode Satuan</th>
            <th>Kategori Barang</th>
            <th>Status Barang</th>
            <th>WAC</th>
            <th>Stok</th>
            <th>Amount</th>
            <th>Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rs as $row)
            @php
                $no++;
                $row = FieldsToObject($row);

                $is_aktif = $row->is_aktif == 't' ? 'Aktif' : 'Tidak Aktif';
                $tot_amount += $row->amount;
            @endphp

            <tr>
                <td align="center">{{ $no }}</td>
                <td align="center">{{ $row->kode_brg }}</td>
                <td>{{ $row->nama_brg }}</td>
                <td align="center">{{ $row->kode_satuan }}</td>
                <td align="center">{{ $row->kel_brg }}</td>
                <td align="center">{{ $is_aktif }}</td>
                <td align="right">{{ format_uang($row->wac, 2) }}</td>
                <td align="right">{{ floatval($row->stock) }}</td>
                <td align="right">{{ format_uang($row->amount, 2) }}</td>
                <td align="center">
                    <a href="javascript:void(0)" onclick="DetailStock({{ $row->mbid }});">[ Detail ]</a>
                </td>
            </tr>
        @endforeach

        <tr>
            <td align="right" colspan="8"><b>TOTAL AMOUNT</b></td>
            <td align="right"><b>{{ format_uang($tot_amount, 2) }}</b></td>
            <td align="right"><b>&nbsp;</b></td>
        </tr>
    </tbody>
</table>
@endsection