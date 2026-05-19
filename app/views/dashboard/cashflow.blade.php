@extends('layouts.main')

@push('css')
<style type="text/css">
    /* Kustomisasi halus untuk melengkapi utility classes */
    .kpi-card {
        transition: transform 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<!-- BARIS FILTER UTAMA -->
<div class="d-flex flex-column flex-lg-row mb-6">
    <div class="flex-lg-row-fluid">
        <div class="card border border-gray-300 rounded-1 p-0">
            <div id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-wallet text-primary me-4 fs-1"></span>
                                Executive Cashflow Dashboard 
                                <span class="text-muted fw-normal ms-2 fs-5">| Financial Director View</span>
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-cf" class="p-6">
                    @csrf
                    <div class="row g-4 align-items-end">
                        <div class="col-lg-4">
                            <label class="text-dark fw-bold fs-7 pb-2">Cabang / Konsolidasi</label>
                            {!! $cmb_cabang !!}
                        </div>

                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2" required>
                                {!! get_combo_option_month_long(date('m')) !!}
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2" required>
                                {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <button type="button" class="btn btn-sm btn-primary rounded-1 w-100 fw-bold" id="btnView">
                                <i class="la la-sync-alt fs-6"></i> Refresh Analisis
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-4 border-top border-gray-200">
                        <label>
                            <input type="radio" class="btn-check" name="byData" value="yoy" checked />
                            <span class="btn btn-sm btn-light btn-active-dark fw-bold px-4">Kinerja Bulanan (YoY)</span>
                        </label>
                        <label>
                            <input type="radio" class="btn-check" name="byData" value="ytd" />
                            <span class="btn btn-sm btn-light btn-active-dark fw-bold px-4">Kinerja Akumulatif (YTD)</span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- BARIS 1: KPI SUMMARY CARDS (UX: Makro Indikator Likuiditas) -->
<div class="row g-5 mb-6">
    <!-- Card 1: Saldo Kas Akhir -->
    <div class="col-md-6 col-xl-3">
        <div class="card border border-gray-300 rounded-1 p-5 kpi-card bg-light-primary">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-gray-700 fw-bold fs-7">Ending Cash Balance</span>
                <span class="badge badge-light-primary fw-bolder">Target 95%</span>
            </div>
            <div class="fs-2x fw-bolder text-dark mb-1">Rp 45,20 M</div>
            <div class="text-success fs-7 fw-bold">
                <i class="la la-arrow-up fs-6 text-success"></i> +8.4% <span class="text-gray-500 fw-normal">vs bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Net Cash Flow -->
    <div class="col-md-6 col-xl-3">
        <div class="card border border-gray-300 rounded-1 p-5 kpi-card bg-light-success">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-gray-700 fw-bold fs-7">Net Cash Flow</span>
                <span class="las la-exchange-alt text-success fs-3"></span>
            </div>
            <div class="fs-2x fw-bolder text-success mb-1">+Rp 5,12 M</div>
            <div class="text-success fs-7 fw-bold">
                <span class="text-gray-600 fw-normal">Surplus Bulan Ini</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Cash Inflow -->
    <div class="col-md-6 col-xl-3">
        <div class="card border border-gray-300 rounded-1 p-5 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-gray-700 fw-bold fs-7">Total Cash Inflow</span>
                <i class="la la-arrow-down text-primary fs-3"></i>
            </div>
            <div class="fs-2x fw-bolder text-dark mb-1">Rp 120,45 M</div>
            <div class="text-muted fs-7">Efisiensi Penagihan AR: <strong class="text-dark">92%</strong></div>
        </div>
    </div>

    <!-- Card 4: Total Cash Outflow -->
    <div class="col-md-6 col-xl-3">
        <div class="card border border-gray-300 rounded-1 p-5 kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-gray-700 fw-bold fs-7">Total Cash Outflow</span>
                <i class="la la-arrow-up text-danger fs-3"></i>
            </div>
            <div class="fs-2x fw-bolder text-dark mb-1">Rp 115,33 M</div>
            <div class="text-danger fs-7 fw-bold">
                <span class="text-gray-500 fw-normal">OPEX Dominan: </span> Rp 72 M
            </div>
        </div>
    </div>
</div>

<!-- BARIS 2: VISUALISASI GRAFIK (UX: Analisis Tren & Komposisi) -->
<div class="row g-5 mb-6">
    <!-- Tren Arus Kas -->
    <div class="col-lg-8">
        <div class="card border border-gray-300 rounded-1 p-6 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title text-dark fw-bolder fs-5 mb-0">Tren Inflow vs Outflow</h3>
                <span class="text-muted fs-7">6 Bulan Terakhir</span>
            </div>
            <!-- Canvas/Div Komponen Chart (misal menggunakan ApexCharts / Chart.js) -->
            <div id="chart_cashflow_trend" style="height: 300px;" class="d-flex align-items-center justify-content-center bg-light rounded border border-dashed">
                <span class="text-muted fs-7">[ Area Chart: Inflow vs Outflow vs Net Cash Line ]</span>
            </div>
        </div>
    </div>

    <!-- Komposisi Alokasi Kas -->
    <div class="col-lg-4">
        <div class="card border border-gray-300 rounded-1 p-6 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title text-dark fw-bolder fs-5 mb-0">Komposisi Arus Kas</h3>
            </div>
            <!-- Canvas/Div Komponen Donut Chart -->
            <div id="chart_cashflow_donut" style="height: 300px;" class="d-flex align-items-center justify-content-center bg-light rounded border border-dashed">
                <span class="text-muted fs-7">[ Donut Chart: Operasi vs Investasi vs Pendanaan ]</span>
            </div>
        </div>
    </div>
</div>

<!-- BARIS 3: RINGKASAN AKTIVITAS UTAMA (UX: Struktur Direct Method yang Terbaca Cepat) -->
<div class="card border border-gray-300 rounded-1 p-6">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="text-dark fw-bolder fs-4 mb-0">Ringkasan Aktivitas Arus Kas (Metode Langsung)</h3>
        <button class="btn btn-sm btn-light-primary fw-bold"><i class="la la-file-download"></i> Export PDF</button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
            <thead>
                <tr class="fw-bolder text-muted fs-7 text-uppercase bg-light">
                    <th class="ps-3 rounded-start">Aktivitas Kas</th>
                    <th class="text-end">Bulan Ini</th>
                    <th class="text-end">Bulan Lalu</th>
                    <th class="text-end rounded-end pe-3">Varians (%)</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. OPERASIONAL -->
                <tr class="fw-bold fs-6 text-dark">
                    <td class="ps-3 text-primary"><i class="las la-arrow-right text-primary"></i> 1. Arus Kas dari Aktivitas Operasi</td>
                    <td class="text-end">Rp 42,10 M</td>
                    <td class="text-end">Rp 38,50 M</td>
                    <td class="text-end text-success pe-3">+9.3%</td>
                </tr>
                <tr class="text-gray-600 fs-7">
                    <td class="ps-6">• Penerimaan dari Pelanggan (AR)</td>
                    <td class="text-end">Rp 112,00 M</td>
                    <td class="text-end">Rp 105,00 M</td>
                    <td class="text-end pe-3">+6.6%</td>
                </tr>
                <tr class="text-gray-600 fs-7">
                    <td class="ps-6">• Pembayaran kepada Pemasok & Vendor</td>
                    <td class="text-end">(Rp 55,00 M)</td>
                    <td class="text-end">(Rp 52,00 M)</td>
                    <td class="text-end pe-3">-5.7%</td>
                </tr>
                <tr class="text-gray-600 fs-7">
                    <td class="ps-6">• Pembayaran Gaji & Operasional Kantor</td>
                    <td class="text-end">(Rp 14,90 M)</td>
                    <td class="text-end">(Rp 14,50 M)</td>
                    <td class="text-end pe-3">-2.7%</td>
                </tr>

                <!-- 2. INVESTASI -->
                <tr class="fw-bold fs-6 text-dark border-top">
                    <td class="ps-3 text-warning"><i class="las la-arrow-right text-warning"></i> 2. Arus Kas dari Aktivitas Investasi</td>
                    <td class="text-end">(Rp 22,00 M)</td>
                    <td class="text-end">(Rp 5,00 M)</td>
                    <td class="text-end text-danger pe-3">-340.0%</td>
                </tr>
                <tr class="text-gray-600 fs-7">
                    <td class="ps-6">• Perolehan Aset Tetap / CAPEX</td>
                    <td class="text-end">(Rp 22,00 M)</td>
                    <td class="text-end">(Rp 5,00 M)</td>
                    <td class="text-end pe-3">-340.0%</td>
                </tr>

                <!-- 3. PENDANAAN -->
                <tr class="fw-bold fs-6 text-dark border-top">
                    <td class="ps-3 text-info"><i class="las la-arrow-right text-info"></i> 3. Arus Kas dari Aktivitas Pendanaan</td>
                    <td class="text-end">(Rp 14,98 M)</td>
                    <td class="text-end">(Rp 12,00 M)</td>
                    <td class="text-end text-danger pe-3">-24.8%</td>
                </tr>
                <tr class="text-gray-600 fs-7">
                    <td class="ps-6">• Pembayaran Dividen atau Pinjaman Bank</td>
                    <td class="text-end">(Rp 14,98 M)</td>
                    <td class="text-end">(Rp 12,00 M)</td>
                    <td class="text-end pe-3">-24.8%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        // Logika inisialisasi chart atau interaksi select2 Anda diletakkan di sini.
    });
</script>
@endpush