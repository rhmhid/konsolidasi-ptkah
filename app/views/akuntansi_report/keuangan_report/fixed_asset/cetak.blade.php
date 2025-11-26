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
    Laporan Fixed Asset
    <span style="text-transform: uppercase;">Periode : {{ monthnamelong($data['smonth']) }} {{ $data['syear'] }}</span>
</h2>

<table width="100%" class="bdr2 pad">
    <thead>
        <tr>
            <th>Kategori Asset</th>
            <th>Kode Asset</th>
            <th>Nama Asset</th>
            <th>Tanggal Efektif</th>
            <th>Umur ( Bulan )</th>
            <th>Lokasi</th>
            <th>Jumlah Penyusutan</th>
            <th>Nilai Perolehan</th>
            <th>Nilai Minimum</th>
            <th>Akumulasi</th>
            <th>Nilai Buku</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $hasData = false;
            $tot_perolehan = $tot_minimum = $tot_akumulasi = $tot_buku = 0;
        @endphp

        @forelse ($rs as $row)
            @php
                $hasData = true;
                $row = FieldsToObject($row);

                $umur_thn = floor($row->masa_manfaat / 12);
                $umur_bln = ($row->masa_manfaat % 12);
                $masa_manfaat = "";

                if ($umur_thn <> 0) $masa_manfaat .= $umur_thn.' Tahun ';

                if ($umur_bln <> 0) $masa_manfaat .= $umur_bln.' Bulan';

                $nilai_buku = $row->nilai_perolehan - $row->nilai_minimum - $row->akumulasi;

                $tot_perolehan += $row->nilai_perolehan;
                $tot_minimum += $row->nilai_minimum;
                $tot_akumulasi += $row->akumulasi;
                $tot_buku += $nilai_buku;
            @endphp

            <tr>
                <td align="center">{{ $row->nama_kategori }}</td>
                <td align="center">{{ $row->facode }}</td>
                <td>{{ $row->faname }}</td>
                <td align="center">{{ dbtstamp2stringina($row->fadate) }}</td>
                <td align="center">{{ $masa_manfaat }}</td>
                <td align="center">{{ $row->lokasi_nama }}</td>
                <td align="center">{{ $row->dpr_count }}</td>
                <td align="right">{{ format_uang($row->nilai_perolehan, 2) }}</td>
                <td align="right">{{ format_uang($row->nilai_minimum, 2) }}</td>
                <td align="right">{{ format_uang($row->akumulasi, 2) }}</td>
                <td align="right">{{ format_uang($nilai_buku, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" align="center"><em>Tidak ada data untuk ditampilkan.</em></td>
            </tr>
        @endforelse

        @if ($hasData)
            <tr>
                <td colspan="7" align="right"><b>TOTAL</b></td>
                <td align="right"><b>{{ format_uang($tot_perolehan, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_minimum, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_akumulasi, 2) }}</b></td>
                <td align="right"><b>{{ format_uang($tot_buku, 2) }}</b></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection