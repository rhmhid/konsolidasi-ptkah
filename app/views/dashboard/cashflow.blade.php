@extends('layouts.main')

@push('css')
<style type="text/css">
    .dashboard-container {
        color: #181c32;
        font-family: 'Inter', 'Poppins', sans-serif;
    }

    .italic-title {
        font-family: 'Playfair Display', serif;
        font-style: italic;
    }

    .card-custom-border {
        background-color: #ffffff;
        border: 1px solid #eff2f5 !important;
    }

    .accent-operasi {
        border-top: 3px solid #0d9488 !important;
    }

    .accent-investasi {
        border-top: 3px solid #d97706 !important;
    }

    .accent-pendanaan {
        border-top: 3px solid #2563eb !important;
    }

    .accent-saldo {
        border-top: 3px solid #6d28d9 !important;
    }

    .badge-soft-success {
        background-color: #ecfdf5;
        color: #065f46;
        font-weight: 700;
    }

    .badge-soft-warning {
        background-color: #fffbeb;
        color: #92400e;
        font-weight: 700;
    }

    .badge-soft-blue {
        background-color: #eff6ff;
        color: #1e40af;
        font-weight: 700;
    }

    .badge-soft-purple {
        background-color: #f5f3ff;
        color: #5b21b6;
        font-weight: 700;
    }

    .wf-bar-container {
        border-radius: 4px;
        position: relative;
        width: 120px;
    }

    .wf-bar-fill {
        align-items: center;
        border-radius: 4px;
        display: flex;
        font-size: 11px;
        font-weight: bold;
        height: 24px;
        padding-left: 8px;
    }

    .wf-fill-success {
        background-color: #a7f3d0;
        color: #064e3b;
    }

    .wf-fill-danger {
        background-color: #fecdd3;
        color: #9f1239;
    }

    .wf-fill-blue {
        background-color: #bfdbfe;
        color: #1e3a8a;
    }

    .badge-premium-status {
        border-radius: 0.45rem;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.5rem 1rem;
    }

    .badge-premium-primary {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
    }

    .badge-premium-success {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #059669;
    }

    .badge-premium-warning {
        background-color: #fff8dd;
        border: 1px solid #fde68a;
        color: #b45309;
    }

    .btn-kinerja-active {
        background-color: #181c32 !important;
        color: #ffffff !important;
        font-weight: bold;
    }

    .btn-kinerja-inactive {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
    }

    .bc-header {
        background-color: #f8fafc;
        border-top-left-radius: inherit;
        border-top-right-radius: inherit;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="d-flex flex-column flex-lg-row mb-6">
        <div class="flex-lg-row-fluid">
            <div class="card card-custom-border rounded-2 p-0 shadow-sm">
                <div id="kt_content_header">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 p-5 px-7">
                        <div>
                            <h1 class="text-dark fw-bolder fs-1x my-1 d-flex align-items-center">
                                <span class="las la-chart-pie text-dark me-3 fs-1"></span> 
                                <span class="text-dark fs-1">Cashflow</span> 
                                <span class="text-muted fw-normal ms-2 fs-3">| Dashboard Eksekutif</span>
                            </h1>
                        </div>

                        <div class="d-flex align-items-center gap-2 my-1">
                            <span class="badge badge-premium-status badge-premium-primary">
                                <i class="las la-hospital text-dark me-1"></i> <span class="cabang-span">Cabang Name</span>
                            </span>

                            <span class="badge badge-premium-status badge-premium-success">
                                <i class="las la-calendar text-success me-1"></i> <span class="periode-span">Periode</span>
                            </span>

                            <span class="badge badge-premium-status badge-premium-warning">
                                <i class="las la-file-alt text-warning me-1"></i> Metode Langsung
                            </span>
                        </div>
                    </div>

                    <form method="post" id="form-cf" class="p-6 pb-4 bg-white">
                        <div class="row g-4 align-items-end">
                            <div class="col-lg-4">
                                <label class="text-dark fw-bold fs-7 pb-2">Cabang Konsolidasi</label>
                                {!! $cmb_cabang !!}
                            </div>

                            <div class="col-lg-3">
                                <label class="text-dark fw-bold fs-7 pb-2">Periode Bulan</label>
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
                                <button type="button" class="btn btn-sm btn-dark rounded-1 w-100 fw-bold py-3" id="btnView">
                                    <i class="la la-search"></i> Analisis Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-6">
        <div class="col-md-6 col-xl-3">
            <div class="card card-custom-border accent-operasi rounded-2 p-5 shadow-sm">
                <span class="text-muted fw-bold fs-8 text-uppercase tracking-wide">Arus Kas Operasional</span>
                <div class="fs-1 fw-bolder text-dark my-1 arus-kas-operasional">Rp. 0</div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <span class="badge badge-soft-success px-2 py-1 fs-8">Positif ✓</span>
                    <span class="text-gray-500 fs-7">Inflow: <strong class="text-dark inflow-operasional">Rp. 0</strong></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-custom-border accent-investasi rounded-2 p-5 shadow-sm">
                <span class="text-muted fw-bold fs-8 text-uppercase tracking-wide">Arus Kas Investasi</span>
                <div class="fs-1 fw-bolder text-dark my-1 arus-kas-investasi">Rp. 0</div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <span class="badge badge-soft-warning px-2 py-1 fs-8">Tidak Ada</span>
                    <span class="text-gray-400 fs-7">Pengeluaran Aset</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-custom-border accent-pendanaan rounded-2 p-5 shadow-sm">
                <span class="text-muted fw-bold fs-8 text-uppercase tracking-wide">Arus Kas Pendanaan</span>
                <div class="fs-1 fw-bolder text-dark my-1 arus-kas-pendanaan">Rp. 0</div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <span class="badge badge-soft-blue px-2 py-1 fs-8">Tidak Ada</span>
                    <span class="text-gray-400 fs-7">Bank & Provision</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card card-custom-border accent-saldo rounded-2 p-5 shadow-sm">
                <span class="text-muted fw-bold fs-8 text-uppercase tracking-wide">Saldo Kas Akhir</span>
                <div class="fs-1 fw-bolder text-dark my-1 saldo-akhir">Rp. 0</div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <span class="badge badge-soft-purple px-2 py-1 fs-8 saldo-diff">▲ Rp. 0</span>
                    <span class="text-gray-500 fs-7">Awal: <strong class="text-dark saldo-awal">Rp. 0</strong></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-7">
            <div class="card card-custom-border rounded-2 p-0 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 p-5 px-6 bc-header">
                    <h3 class="text-dark fw-bolder fs-4 m-0">Waterfall Arus Kas — <span class="cabang-span">Cabang Name</span></h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-bold fs-7 d-flex align-items-center">
                            Metode Langsung <span class="mx-2 text-gray-300">&middot;</span> 
                            <i class="las la-calendar text-muted me-1 fs-5"></i> <span class="periode-span">Periode</span>
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="table-responsive">
                        <table class="table align-middle gs-0 gy-3 border-0 m-0">
                            <tbody>
                                <tr>
                                    <td class="text-gray-700 fw-bold fs-6 w-200px">Inflow Operasional</td>
                                    <td class="w-200px">
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill wf-fill-success inflow-operasional" style="width: 80%;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark fw-bold fs-6 inflow-operasional-full">Rp. 0</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-700 fw-bold fs-6">Outflow Operasional</td>
                                    <td>
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill wf-fill-danger outflow-operasional" style="width: 45%;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted fs-6 outflow-operasional-full">Rp. 0</td>
                                </tr>
                                <tr class="border-top border-gray-200">
                                    <td class="text-gray-800 fw-bolder fs-6">Net Kas Operasional</td>
                                    <td>
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill wf-fill-blue arus-kas-operasional" style="width: 55%;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark fw-bolder fs-6 arus-kas-operasional-full">Rp. 0</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-700 fw-bold fs-6">Net Kas Investasi</td>
                                    <td>
                                        <span class="badge badge-light-secondary px-3 py-1 fw-bold arus-kas-investasi">Rp. 0</span>
                                    </td>
                                    <td class="text-end text-dark fs-6 arus-kas-investasi-full">Rp. 0</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-700 fw-bold fs-6">Net Kas Pendanaan</td>
                                    <td>
                                        <span class="badge badge-light-secondary px-3 py-1 fw-bold arus-kas-pendanaan">Rp. 0</span>
                                    </td>
                                    <td class="text-end text-dark fs-6 arus-kas-pendanaan-full">Rp. 0</td>
                                </tr>
                                <tr class="border-top border-gray-200">
                                    <td class="text-gray-800 fw-bolder fs-6">Net Perubahan Kas</td>
                                    <td>
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill wf-fill-success saldo-diff" style="width: 55%;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark fw-bolder fs-6 saldo-diff-full">Rp. 0</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-700 fw-bold fs-6">Saldo Awal</td>
                                    <td>
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill bg-light-primary text-primary saldo-awal" style="width: 85%;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark fw-bold fs-6 saldo-awal-full">Rp. 0</td>
                                </tr>
                                <tr class="bg-light-sticky rounded-2">
                                    <td class="text-gray-700 fw-bold fs-6">Saldo Kas Akhir</td>
                                    <td>
                                        <div class="wf-bar-container w-100">
                                            <div class="wf-bar-fill text-purple saldo-akhir" style="width: 100%; background-color: #f5f3ff !important; color: #5b21b6 !important;">Rp. 0</div>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark fw-bold fs-6 saldo-akhir-full">Rp. 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-custom-border rounded-2 p-0 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center border-bottom border-gray-200 p-5 px-6 bc-header">
                        <h3 class="text-dark fw-bolder fs-5 m-0">Saldo Kas <span class="cabang-span">Cabang Name</span></h3>
                    </div>

                    <div class="p-6 pb-0">
                        <div style="position: relative; height: 240px; width: 100%;">
                            <canvas id="lrCashflowChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 mt-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-4 bg-light rounded-2 border-start border-primary border-4">
                                <div class="text-muted fs-8 text-uppercase fw-bold">Saldo Awal</div>
                                <div class="fs-4 fw-bolder text-dark saldo-awal">Rp. 0</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-4 bg-light rounded-2 border-start border-success border-4">
                                <div class="text-muted fs-8 text-uppercase fw-bold">Saldo Akhir</div>
                                <div class="fs-4 fw-bolder text-dark saldo-akhir">Rp. 0</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 rounded-2 bg-light-success d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-7">Net Perubahan Kas</span>
                        <span class="fs-4 fw-bolder text-success saldo-diff">+Rp. 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $('#btnView').click(function (e)
    {
        e.preventDefault()

        const $form   = $('#form-cf')
        const $sBid   = $form.find('[id="s-Bid"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear  = $form.find('[id="s-Year"]').val()

        if (!$sBid)
        {
            swalShowMessage('Perhatian!', "Cabang Harus Dipilih.", 'warning')
            return false
        }

        if (!$sMonth)
        {
            swalShowMessage('Perhatian!', "Bulan Harus Dipilih.", 'warning')
            return false
        }

        if (!$sYear)
        {
            swalShowMessage('Perhatian!', "Tahun Harus Dipilih.", 'warning')
            return false
        }

        let $btn = $(this)
        let originalText = $btn.html()

        $btn.html('<i class="las la-spinner la-spin"></i> Memuat...').prop('disabled', true)

        $.ajax({
            url: "{{ route('api.dashboard.cashflow.data') }}",
            type: "GET",
            data: {
                bid: $sBid,
                month: $sMonth,
                year: $sYear
            },
            dataType: "json",
            success: function (response)
                    {
                        $('.cabang-span').html(response.branch.branch_name)

                        let $periodeCF = response.bulan + ' ' + response.year
                        $('.periode-span').html($periodeCF)

                        let $DataCashFlow = response.data_cashflow
                        let $DataSaldo = response.data_saldo

                        $('.arus-kas-operasional').html(formatKeJT($DataCashFlow.arus_kas_operasional))
                        $('.arus-kas-operasional-full').html('Rp. ' + MoneyFormat($DataCashFlow.arus_kas_operasional))
                        
                        $('.inflow-operasional').html(formatKeJT($DataCashFlow.inflow_operasional))
                        $('.inflow-operasional-full').html('Rp. ' + MoneyFormat($DataCashFlow.inflow_operasional))
                        
                        $('.outflow-operasional').html(formatKeJT($DataCashFlow.outflow_operasional))
                        $('.outflow-operasional-full').html('Rp. ' + MoneyFormat($DataCashFlow.outflow_operasional))

                        $('.arus-kas-investasi').html(formatKeJT($DataCashFlow.arus_kas_investasi))
                        $('.arus-kas-investasi-full').html('Rp. ' + MoneyFormat($DataCashFlow.arus_kas_investasi))
                        
                        $('.arus-kas-pendanaan').html(formatKeJT($DataCashFlow.arus_kas_pendanaan))
                        $('.arus-kas-pendanaan-full').html('Rp. ' + MoneyFormat($DataCashFlow.arus_kas_pendanaan))

                        $('.saldo-awal').html(formatKeJT($DataSaldo.awal))
                        $('.saldo-awal-full').html('Rp. ' + MoneyFormat($DataSaldo.awal))
                        
                        $('.saldo-akhir').html(formatKeJT($DataSaldo.akhir))
                        $('.saldo-akhir-full').html('Rp. ' + MoneyFormat($DataSaldo.akhir))
                        
                        $('.saldo-diff').html(formatKeJT($DataSaldo.diff))
                        $('.saldo-diff-full').html('Rp. ' + MoneyFormat($DataSaldo.diff))

                        let $chartDataCabang = [
                            $DataSaldo.awal,
                            $DataCashFlow.arus_kas_operasional,
                            $DataCashFlow.arus_kas_investasi,
                            $DataCashFlow.arus_kas_pendanaan,
                            $DataSaldo.akhir
                        ]

                        let $chartColors = [
                            '#3b82f6',
                            $DataCashFlow.arus_kas_operasional >= 0 ? '#10b981' : '#ef4444',
                            $DataCashFlow.arus_kas_investasi >= 0 ? '#10b981' : '#ef4444',
                            $DataCashFlow.arus_kas_pendanaan >= 0 ? '#10b981' : '#ef4444',
                            '#8b5cf6' 
                        ]

                        lrCashflowChartInstance.data.datasets[0].data = $chartDataCabang
                        lrCashflowChartInstance.data.datasets[0].backgroundColor = $chartColors
                        lrCashflowChartInstance.update()
                    },
            error: function (xhr)
                {
                    swalShowMessage('Error!', "Gagal mengambil data dari server.", 'error')
                },
            complete: function ()
                {
                    $btn.html(originalText).prop('disabled', false)
                }
        })
    })

    let lrCashflowChartInstance = new Chart(document.getElementById('lrCashflowChart'), {
        type: 'bar',
        data: {
            labels: ['Saldo Awal', 'Net Operasi', 'Net Investasi', 'Net Pendanaan', 'Saldo Akhir'],
            datasets: [{
                label: 'Arus Kas',
                data: [],
                backgroundColor: [],
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { 
                    top: 25 
                }
            },
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    callbacks: {
                        label: function (context)
                            {
                                let rawValue = context.raw || 0

                                return ' ' + formatKeJT(rawValue)
                            }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#64748b',
                    font: { 
                        size: 9, 
                        weight: '600' 
                    },
                    formatter: function (value)
                        {
                            return formatKeJT(value)
                        }
                }
            },
            scales: {
                x: {
                    grid: { 
                        display: false 
                    },
                    ticks: { 
                        color: '#64748b', 
                        font: { size: 9 },
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    grid: { 
                        color: '#f1f5f9' 
                    },
                    beginAtZero: true,
                    grace: '15%',
                    ticks: {
                        color: '#64748b',
                        font: { size: 9 },
                        callback: function (value)
                            {
                                return formatKeJT(value)
                            }
                    }
                }
            }
        }
    })
</script>
@endpush