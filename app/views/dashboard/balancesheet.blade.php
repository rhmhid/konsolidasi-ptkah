@extends('layouts.main')

<style>

:root{
  --navy:#0B1A2E;--navy2:#162848;--teal:#0FA896;--teal2:#0D8E7D;
  --gold:#E8A820;--red:#E24B4A;--green:#2E9E5B;--blue:#2B7CE9;
  --bg:#F2F5F9;--card:#fff;--border:#DDE3EC;
  --txt:#18283A;--txt2:#4A6070;--txt3:#8999AA;
}


/* CONTENT */
.content{padding:18px 22px;max-width:2300px;margin:0 auto}

/* KPI */
.kpi-row{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:18px}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:13px 15px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;background:var(--ac,var(--teal))}
.kpi-label{font-size:9.5px;color:var(--txt3);text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px}
.kpi-value{font-size:15px;font-weight:700;color:var(--txt);line-height:1.2}
.kpi-sub{font-size:9.5px;margin-top:5px;color:var(--txt3)}
.kpi-badge{display:inline-block;font-size:9px;font-weight:600;padding:2px 8px;border-radius:8px;margin-top:4px}
.badge-up{background:rgba(46,158,91,.12);color:#2E9E5B}
.badge-down{background:rgba(226,75,74,.12);color:#E24B4A}
.badge-same{background:var(--bg);color:var(--txt3);border:1px solid var(--border)}

/* CARD */
.card{background:var(--card);border:1px solid var(--border);border-radius:10px; padding: 10px}
.sec-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:13px}
.sec-title{font-size:13px;font-weight:600;color:var(--txt)}
.sec-tag{font-size:9.5px;color:var(--txt3);background:var(--bg);border:1px solid var(--border);padding:3px 9px;border-radius:10px}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:13px;margin-bottom:16px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:13px;margin-bottom:16px}
.full{margin-bottom:16px}

/* TABLE */
.ft{width:100%;border-collapse:collapse;font-size:11px}
.ft thead th{padding:6px 9px;text-align:left;font-weight:600;font-size:9.5px;text-transform:uppercase;letter-spacing:.5px;color:var(--txt3);border-bottom:1.5px solid var(--border);background:var(--bg)}
.ft thead th:not(:first-child){text-align:right}
.ft tbody tr:hover{background:rgba(15,168,150,.04)}
.ft td{padding:6px 9px;border-bottom:1px solid var(--border);color:var(--txt2)}
.ft td:not(:first-child){text-align:right;font-variant-numeric:tabular-nums;font-family:'Courier New',monospace;font-size:10.5px}
.ft .row-h{font-weight:600;color:var(--txt)}
.ft .row-tot{font-weight:700;color:var(--txt);background:rgba(15,168,150,.07)}
.ft .sg td{background:var(--navy);color:#fff;font-weight:700;font-size:10px;padding:5px 9px}
@media(prefers-color-scheme:dark){.ft .sg td{background:#1A2E45}}
.ft .ind{padding-left:20px}
.neg{color:#E24B4A}
.pos{color:#2E9E5B}

/* CHARTS */
.chart-wrap{position:relative;width:100%}
.legend-row{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:9px;font-size:10.5px;color:var(--txt2)}
.ldot{width:9px;height:9px;border-radius:2px;display:inline-block;margin-right:4px;vertical-align:middle}

/* PROGRESS */
.prog-row{margin-bottom:10px}
.prog-labels{display:flex;justify-content:space-between;margin-bottom:4px;font-size:10.5px}
.prog-bg{height:8px;background:var(--bg);border-radius:4px;overflow:hidden;border:1px solid var(--border)}
.prog-fill{height:100%;border-radius:4px}

/* RATIO GRID */
.ratio-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px}
.ratio-box{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px;text-align:center}
.r-val{font-size:18px;font-weight:700;color:var(--txt);margin:4px 0 2px;line-height:1}
.r-lbl{font-size:9px;color:var(--txt3);text-transform:uppercase;letter-spacing:.6px}
.r-note{font-size:9px;margin-top:3px;font-weight:600}
.rok{color:#2E9E5B}.rwarn{color:#E8A820}.rbad{color:#E24B4A}

/* COMPARISON TABLE */
.comp-header{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:0;margin-bottom:6px}
.comp-header span{font-size:9px;text-transform:uppercase;letter-spacing:.5px;color:var(--txt3);text-align:right;padding:0 8px}
.comp-header span:first-child{text-align:left;padding-left:0}

/* PAGE */
.page{display:none}.page.active{display:block}

.note-box{background:rgba(232,168,32,.08);border:1px solid rgba(232,168,32,.3);border-radius:8px;padding:10px 13px;font-size:10.5px;color:var(--txt2);margin-top:12px;line-height:1.6}
.note-box strong{color:var(--txt)}

.ai-btn{background:var(--navy);color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:11px;cursor:pointer;font-weight:500;width:100%;margin-top:12px}
.ai-btn:hover{background:var(--navy2)}
    
</style>

@section('content')
<div class="d-flex flex-column flex-lg-row">
    <div class="flex-lg-row-fluid">
        <div class="card border border-gray-300 rounded-1">
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-chart-bar text-primary me-4"></span>
                                Neraca Konsolidasi
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-bs" class="p-6 pb-0">
                    <div class="row g-4 mb-5">
                        <div class="col-lg-4">
                            <label class="text-dark fw-bold fs-7 pb-2">Cabang</label>
                            {!! $cmb_cabang !!}
                        </div>
                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2">
                                {!! $bulana !!}
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2">
                                {!! $tahuna !!}
                            </select>
                        </div>
                        <div class="col-lg-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-primary rounded-1 w-100" id="btnView">
                                <i class="la la-search"></i> Lihat Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>


                <div id="page-konsolidasi" class="page active">
                    <div class="content">
                      <div class="kpi-row">
                        <div class="kpi-card" style="--ac:#0FA896">
                          <div class="kpi-label">Total Aktiva</div>
                          <div class="kpi-value">{!! $total_aktiva !!}</div>
                          <div class="kpi-sub">{!! $total_aktiva_sub !!}</div>
<!--                           <span class="kpi-badge badge-up">▲ +1,13% vs Jan</span> -->
                        </div>
                        <div class="kpi-card" style="--ac:#3B9B5A">
                          <div class="kpi-label">Total Ekuitas</div>
                          <div class="kpi-value">{!! $total_ekuitas !!}</div>
                          <div class="kpi-sub">Laba akumulatif + berjalan</div>
<!--                           <span class="kpi-badge badge-up">▲ Positif</span> -->
                        </div>
                        <div class="kpi-card" style="--ac:#E8A820">
                          <div class="kpi-label">Total Kewajiban</div>
                          <div class="kpi-value">{!! $total_kewajiban !!}</div>
                          <div class="kpi-sub">Jk.Pendek + Jk.Panjang</div>
<!--                           <span class="kpi-badge badge-same">53,4% dari Aset</span> -->
                        </div>
                        <div class="kpi-card" style="--ac:#2B7CE9">
                          <div class="kpi-label">L/R Tahun Berjalan</div>
                          <div class="kpi-value">{!! $total_rl_tahun_berjalan !!}</div>
                          <div class="kpi-sub">{!! $total_rl_tahun_berjalan_sub !!}</div>
<!--                           <span class="kpi-badge badge-up">▲ Laba</span> -->
                        </div>
                        <div class="kpi-card" style="--ac:#E24B4A">
                          <div class="kpi-label">Hutang Jk. Panjang</div>
                          <div class="kpi-value">{!! $total_ht_jangka_panjang !!}</div>
                          <div class="kpi-sub">Dominasi struktur liabilitas</div>
<!--                           <span class="kpi-badge badge-down">38,9% dari Aset</span> -->
                        </div>
                      </div>

                      <div class="full card">
                        <div class="sec-hdr">
                          <div class="sec-title">Neraca Konsolidasi — Total All Branch</div>
                          <div class="sec-tag">dalam Rupiah (Rp) · {!! $bulan_nama !!} {!! $tahun!!}</div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                                <table class="ft">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Keterangan</th>
                                            {!! $bid !!}
                                            @foreach ($data_cabang as $bc => $cabang)
                                                <th class="sg"  style="{!! $hide_jkk !!}" >{{ $cabang['branch_name'] }}</th>
                                            @endforeach

                                            @if(count($data_cabang) > 1)
                                                <th>Total All Branch</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            @foreach ($data_cabang as $bc => $cabang)
                                                <th style="text-align: right; {!! $hide_jkk !!}">{{ $bln }}</th>
                                            @endforeach

                                            @if(count($data_cabang) > 1)
                                                <th>{{ $bln }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$empty_aktiva)
                                            @foreach ($data_pos[1] as $row)
                                                @php
                                                    $row = FieldsToObject($row);
                                                    $amounts = json_decode(json_encode($row->amounts), true);
                                                @endphp

                                                <tr>
                                                    <td class="sg">{!! $row->nama_pos !!}</td>

                                                    @foreach ($data_cabang as $bc => $cabang)
                                                        <td align="right"  style="{!! $hide_jkk !!}">{!! $amounts['branches'][$bc]['amount'] ?? '' !!}</td>
                                                    @endforeach

                                                    @if(count($data_cabang) > 1)
                                                        <td align="right">{!! $amounts['total']['amount'] !!}</td>
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
                                            <td><b>POS NERACA LAINNYA</b></td>

                                            @foreach ($data_cabang as $bc => $cabang)
                                                <td align="right"  style="{!! $hide_jkk !!}"><b><u>{!! $pos_lainnya['branches'][$bc]['amount'] ?? '' !!}</u></b></td>
                                                <td align="right"><b><u>{!! $pos_lainnya['branches'][$bc]['amount_prev'] ?? '' !!}</u></b></td>
                                            @endforeach

                                            @if(count($data_cabang) > 1)
                                                <td align="right"  style="{!! $hide_jkk !!}"><b><u>{!! $pos_lainnya['total']['amount'] !!}</u></b></td>
                                                <td align="right"><b><u>{!! $pos_lainnya['total']['amount_prev'] !!}</u></b></td>
                                            @endif
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>


                                <table class="ft">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Keterangan</th>

                                            @foreach ($data_cabang as $bc => $cabang)
                                                <th  style="{!! $hide_jkk !!}">{{ $cabang['branch_name'] }}</th>
                                            @endforeach

                                            @if(count($data_cabang) > 1)
                                                <th>Total All Branch</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            @foreach ($data_cabang as $bc => $cabang)
                                                <th  style=" text-align: right; {!! $hide_jkk !!}">{{ $bln }}</th>
                                            @endforeach

                                            @if(count($data_cabang) > 1)
                                                <th>{{ $bln }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!$empty_pasiva)
                                            @foreach ($data_pos[2] as $row)
                                                @php
                                                    $row = FieldsToObject($row);
                                                    $amounts = json_decode(json_encode($row->amounts), true);
                                                @endphp

                                                <tr>
                                                    <td>{!! $row->nama_pos !!}</td>

                                                    @foreach ($data_cabang as $bc => $cabang)
                                                        <td align="right"  style="{!! $hide_jkk !!}">{!! $amounts['branches'][$bc]['amount'] ?? '' !!}</td>
                                                    @endforeach

                                                    @if(count($data_cabang) > 1)
                                                        <td align="right">{!! $amounts['total']['amount'] !!}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>


                        </div>
                      </div>

                      <div class="two-col">
                        <div class="card">
                          <div class="sec-hdr"><div class="sec-title">Komposisi Aktiva Konsolidasi</div><div class="sec-tag">{!! $bulan_nama !!} {!! $tahun!!}</div></div>
                          <div class="legend-row">

@php
    // Daftar warna berurutan (Index 0 = Top 1, Index 1 = Top 2, dst)
    $list_warna = [
            '#0FA896', // 1. Teal (Utama/Lancar)
            '#E8A820', // 2. Orange (RAU/Perhatian)
            '#2B7CE9', // 3. Blue (Piutang)
            '#3B9B5A', // 4. Green (Persediaan)
            '#E24B4A', // 5. Red (Penting/Warning)
            '#8E44AD', // 6. Purple (Aktiva Tetap)
            '#2C3E50', // 7. Dark Blue (Lain-lain 1)
            '#F39C12', // 8. Amber (Lain-lain 2)
            '#BDC3C7'  // 9. Silver/Grey (Lainnya - Akumulasi)
        ];

    $total_aktiva = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? 1;

@endphp

@foreach($dash_top_3 as $index => $item)
    @php
        // Hapus &nbsp; dan spasi liar
        $nama_bersih = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
        $nilai = $item['amounts']['total']['amount_aktiva'];
        $persen = round(($nilai / $total_aktiva) * 100 ,2);
    @endphp
        <span>
            <span><span class="ldot" style="background:{{ $list_warna[$index] ?? '#cccccc' }}"></span> {{ $nama_bersih }}  {{ $persen }} %</span>
            
        </span>
@endforeach

@php
    $total_nilai_lainnya = array_sum(array_map(function($item) {
        return $item['amounts']['total']['amount_aktiva'] ?? 0;
    }, $dash_lainnya));

    $label_lainnya = formatKeJT($total_nilai_lainnya);
    $warna_lainnya = '#BDC3C7';
@endphp


@foreach($dash_lainnya as $index => $item)
    @php
        // Hapus &nbsp; dan spasi liar
        $persen_lainnya = round(($total_nilai_lainnya / $total_aktiva) * 100 , 2);
    @endphp
@endforeach
        <span>
            <span><span class="ldot" style="background:#BDC3C7"></span> LAINNYA {{ $persen_lainnya }} % </span>
            
        </span>
                          </div>
                          <div class="chart-wrap" style="height:200px">
                            <canvas id="k-aktiva" role="img" aria-label="Komposisi aktiva konsolidasi" width="587" height="200" style="display: block; box-sizing: border-box; height: 200px; width: 587px;"></canvas>
                          </div>
                        </div>
                        <div class="card">
                          <div class="sec-hdr"><div class="sec-title">Komposisi Pasiva Konsolidasi</div><div class="sec-tag">{!! $bulan_nama !!} {!! $tahun!!}</div></div>
                          <div class="legend-row">

@php
    // Daftar warna berurutan (Index 0 = Top 1, Index 1 = Top 2, dst)
    $list_warna_pasiva = [
            '#0FA896', // 1. Teal (Utama/Lancar)
            '#E8A820', // 2. Orange (RAU/Perhatian)
            '#2B7CE9', // 3. Blue (Piutang)
            '#3B9B5A', // 4. Green (Persediaan)
            '#E24B4A', // 5. Red (Penting/Warning)
            '#8E44AD', // 6. Purple (Aktiva Tetap)
            '#2C3E50', // 7. Dark Blue (Lain-lain 1)
            '#F39C12', // 8. Amber (Lain-lain 2)
            '#BDC3C7'  // 9. Silver/Grey (Lainnya - Akumulasi)
        ];

    $total_pasiva = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? 1;

@endphp

@foreach($dash_pasiva_top_3 as $index => $item)
    @php
        // Hapus &nbsp; dan spasi liar
        $nama_bersih = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
        $nilai = $item['amounts']['total']['amount_aktiva'];
        $persen_pasiva = round(($nilai / $total_pasiva) * 100 ,2);
    @endphp
        <span>
            <span><span class="ldot" style="background:{{ $list_warna_pasiva[$index] ?? '#cccccc' }}"></span> {{ $nama_bersih }}  {{ $persen_pasiva }} %</span>
            
        </span>
@endforeach


@php
    $total_nilai_pasiva_lainnya = array_sum(array_map(function($item) {
        return $item['amounts']['total']['amount_aktiva'] ?? 0;
    }, $dash_lainnya));

    $label_lainnya = formatKeJT($total_nilai_pasiva_lainnya);
    $warna_lainnya = '#BDC3C7';
@endphp


@foreach($dash_pasiva_lainnya as $index => $item)
    @php
        // Hapus &nbsp; dan spasi liar
        $persen_lainnya = round(($total_nilai_pasiva_lainnya / $total_aktiva) * 100 , 2);
    @endphp
@endforeach
        <span>
            <span><span class="ldot" style="background:#BDC3C7"></span> LAINNYA {{ $persen_lainnya }} % </span>
            
        </span>


                          </div>
                          <div class="chart-wrap" style="height:200px">
                            <canvas id="k-pasiva" role="img" aria-label="Komposisi pasiva konsolidasi" width="587" height="200" style="display: block; box-sizing: border-box; height: 200px; width: 587px;"></canvas>
                          </div>
                        </div>
                      </div>

                      <div class="two-col">
                        <div class="card">
                          <div class="sec-title" style="margin-bottom:13px">Struktur Keuangan Konsolidasi</div>

                          <div class="prog-row">
                                <div class="prog-labels">
                                    <span style="color:var(--txt2)">Aset Lancar</span>
                                    <span style="font-weight:600;color:var(--txt)">{!! $dash_total_asset_lancar !!} ({!! $dash_total_asset_lancar_persen !!}%)</span>
                                </div>

                                <div class="prog-bg">
                                    <div class="prog-fill" style="width:{!! $dash_total_asset_lancar_persen !!}%;background:#0FA896"></div>
                                </div>
                            </div>

                          <div class="prog-row">
                                <div class="prog-labels">
                                    <span style="color:var(--txt2)">Aset Tidak Lancar (neto)</span>
                                    <span style="font-weight:600;color:var(--txt)">{!! $dash_total_asset_tidak_lancar !!} ({!! $dash_total_asset_tidak_lancar_persen !!}%)</span>
                                </div>
                                <div class="prog-bg">
                                    <div class="prog-fill" style="width:{!! $dash_total_asset_tidak_lancar_persen !!}%;background:#0B1A2E"></div>
                                </div>
                            </div>

                          <div style="height:10px"></div>
                          <div class="prog-row">
                            <div class="prog-labels">
                                <span style="color:var(--txt2)">Kewajiban Jk. Pendek</span>
                                <span style="font-weight:600;color:var(--txt)">{!! $dash_total_kewajiban_jk_pendek !!} ({!! $dash_total_kewajiban_jk_pendek_persen !!}%)</span>
                            </div>
                            <div class="prog-bg">
                                <div class="prog-fill" style="width:{!! $dash_total_kewajiban_jk_pendek_persen !!}%;background:#E8A820"></div>
                            </div>
                        </div>

                          <div class="prog-row">
                            <div class="prog-labels">
                                <span style="color:var(--txt2)">Kewajiban Jk. Panjang</span>
                                <span style="font-weight:600;color:var(--txt)"> {!! $total_ht_jangka_panjang !!}({!! $total_ht_jangka_panjang_persen !!}%)</span>
                            </div>
                            <div class="prog-bg">
                                <div class="prog-fill" style="width:{!! $total_ht_jangka_panjang_persen !!}%;background:#E24B4A"></div>
                            </div>
                        </div>
                          <div class="prog-row">
                            <div class="prog-labels">
                                <span style="color:var(--txt2)">Ekuitas</span>
                                <span style="font-weight:600;color:var(--txt)">{!! $total_ekuitas !!} ({!! $total_ekuitas_persen !!}%)</span>
                            </div>
                            <div class="prog-bg">
                                <div class="prog-fill" style="width:{!! $total_ekuitas_persen !!}%;background:#3B9B5A"></div>
                            </div>
                        </div>
                        </div>
                        <div class="card">
                          <div class="sec-title" style="margin-bottom:12px">Rasio Keuangan — Konsolidasi</div>
                          <div class="ratio-grid">
                            <div class="ratio-box"><div class="r-lbl">Current Ratio</div><div class="r-val rok">{!! $curr_ratio !!}</div></div>
                            <div class="ratio-box"><div class="r-lbl">Debt to Asset</div><div class="r-val rwarn">{!! $dta !!}%</div></div>
                            <div class="ratio-box"><div class="r-lbl">Debt to Equity</div><div class="r-val rwarn">{!! $dte !!}x</div></div>
                            <div class="ratio-box"><div class="r-lbl">Equity Ratio</div><div class="r-val rok">{!! $er !!}%</div></div>
                            <div class="ratio-box"><div class="r-lbl">ROA</div><div class="r-val rok">{!! $roa !!}%</div></div>
                            <div class="ratio-box"><div class="r-lbl">Working Capital</div><div class="r-val rok">{!! $wc !!}</div></div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>




        </div>
    </div>
</div>
@endsection

@push('script')

<script src="{{ asset('assets/js/balancesheet.chart.umd.js.download') }}"></script>
<script>

    $('#btnView').click(function (e)
    {

        e.preventDefault() // batalkan aksi form submit

        const $form = $('#form-bs')
        const $sBid = $form.find('[id="s-Bid"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()


            if ($sMonth == '')
            {
                swalShowMessage('Perhatian!', "Bulan Harus Dipilih.", 'warning')

                return false
            }

            if ($sYear == '')
            {
                swalShowMessage('Perhatian!', "Tahun Harus Dipilih.", 'warning')

                return false
            }

            var $param = 'bid=' + $sBid
                $param += '&month=' + $sMonth
                $param += '&year=' + $sYear

        let $link = "{{ route('dashboard.balancesheet', []) }}"
        window.location.replace($link + '?' + $param);
        return false
    });



const gc=()=>matchMedia('(prefers-color-scheme:dark)').matches?'rgba(255,255,255,.07)':'rgba(0,0,0,.05)';
const tc=()=>matchMedia('(prefers-color-scheme:dark)').matches?'#8899AA':'#6A7A88';
const baseOpts=(stacked)=>({
  responsive:true,maintainAspectRatio:false,
  plugins:{legend:{display:false}},
  scales:{
    x:{stacked:!!stacked,grid:{color:gc()},ticks:{color:tc(),font:{size:9}}},
    y:{stacked:!!stacked,grid:{color:gc()},ticks:{color:tc(),font:{size:9},callback:v=>{
      const a=Math.abs(v);
      return (v<0?'-':'')+( a>=1e9?'Rp '+(a/1e9).toFixed(1)+'M': a>=1e6?'Rp '+(a/1e6).toFixed(0)+'jt':'Rp '+a );
    }}}
  }
});

// KONSOLIDASI AKTIVA
@php
    $labels = [];
    $data_values = [];
    $colors = ['#0FA896','#E8A820','#2B7CE9','#3B9B5A','#E24B4A','#8E44AD','#2C3E50','#F39C12','#BDC3C7']; // Warna sesuai urutan

    // 1. Masukkan Top 3
    foreach($dash_top_3 as $index => $item) {
        $labels[] = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
        $data_values[] = $item['amounts']['total']['amount_aktiva'];
    }

    // 2. Masukkan Lainnya
    $labels[] = 'LAINNYA';
    $data_values[] = array_sum(array_column(array_column(array_column($dash_lainnya, 'amounts'), 'total'), 'amount_aktiva'));
    
    // 3. Total Aktiva untuk Tooltip (Index 14)
    $grand_total = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? array_sum($data_values);
@endphp

new Chart(document.getElementById('k-aktiva'), {
    type: 'doughnut',
    data: {
        // Mengambil array labels dari PHP
        labels: {!! json_encode($labels) !!}, 
        datasets: [{
            // Mengambil array nilai dari PHP
            data: {!! json_encode($data_values) !!},
            backgroundColor: {!! json_encode($colors) !!},
            borderWidth: 2,
            borderColor: 'transparent',
            hoverOffset: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: d => {
                        const v = d.raw;
                        const total = {{ $grand_total }};
                        // Format ke Miliar (M) dan Persen
                        return ' ' + (v / 1e9).toFixed(2) + ' M (' + ((v / total) * 100).toFixed(2) + '%)';
                    }
                }
            }
        }
    }
});



// KONSOLIDASI PASIVA
@php
    $labels = [];
    $data_values = [];
    $colors = ['#0FA896','#E8A820','#2B7CE9','#3B9B5A','#E24B4A','#8E44AD','#2C3E50','#F39C12','#BDC3C7']; // Warna sesuai urutan

    // 1. Masukkan Top 3
    foreach($dash_pasiva_top_3 as $index => $item) {
        $labels[] = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
        $data_values[] = $item['amounts']['total']['amount_aktiva'];
    }

    // 2. Masukkan Lainnya
    $labels[] = 'LAINNYA';
    $data_values[] = array_sum(array_column(array_column(array_column($dash_lainnya, 'amounts'), 'total'), 'amount_aktiva'));
    
    // 3. Total Aktiva untuk Tooltip (Index 14)
    $grand_total = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? array_sum($data_values);
@endphp

new Chart(document.getElementById('k-pasiva'), {
    type: 'doughnut',
    data: {
        // Mengambil array labels dari PHP
        labels: {!! json_encode($labels) !!}, 
        datasets: [{
            // Mengambil array nilai dari PHP
            data: {!! json_encode($data_values) !!},
            backgroundColor: {!! json_encode($colors) !!},
            borderWidth: 2,
            borderColor: 'transparent',
            hoverOffset: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: d => {
                        const v = d.raw;
                        const total = {{ $grand_total }};
                        // Format ke Miliar (M) dan Persen
                        return ' ' + (v / 1e9).toFixed(2) + ' M (' + ((v / total) * 100).toFixed(2) + '%)';
                    }
                }
            }
        }
    }
});
 
</script>

@endpush