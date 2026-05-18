@extends('layouts.main')

@push('css')
<style type="text/css">
    :root {
        --navy:   #0B1A2E;
        --navy2:  #142240;
        --teal:   #0FA896;
        --teal2:  #0D8E7D;
        --gold:   #E8A820;
        --gold2:  #C48A10;
        --red:    #E24B4A;
        --green:  #3B9B5A;
        --bg:     #F4F7FA;
        --card:   #ffffff;
        --border: #D8DFE8;
        --txt:    #1A2A3A;
        --txt2:   #4A6070;
        --txt3:   #8899AA;
    }

    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 20px;
    }

    .full-card {
        margin-bottom: 20px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--txt);
    }

    .card {
        padding: 16px;
    }

    .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 3px;
        height: 100%;
        background: var(--accent-color, var(--teal));
    }

    .kpi-label {
        font-size: 10px;
        color: var(--txt3);
        text-transform: uppercase;
        letter-spacing: .8px;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--txt);
        line-height: 1;
    }

    .chart-wrap {
        position: relative;
        width: 100%;
    }

    .legend-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 10px;
        font-size: 11px;
        color: var(--txt2);
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
        display: inline-block;
        margin-right: 5px;
        vertical-align: middle;
    }

    .tab-row {
        display: flex;
        gap: 2px;
        background: var(--bg);
        border-radius: 8px;
        padding: 3px;
        margin-bottom: 14px;
        border: 1px solid var(--border);
    }

    .tab-btn {
        flex: 1;
        padding: 7px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 11.5px;
        color: var(--txt2);
        border-radius: 6px;
        font-weight: 500;
        transition: all .15s;
    }

    .tab-btn.active {
        background: #fff;
        color: var(--txt);
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-lg-row mb-5">
    <div class="flex-lg-row-fluid">
        <div class="card border border-gray-300 rounded-1 p-0">
            <div id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-chart-bar text-dark me-4"></span>
                                Performance Dashboard (Income Statement)
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-pld" class="p-6 pb-0">
                    <div class="row g-4 mb-5">
                        <div class="col-lg-4">
                            <label class="text-dark fw-bold fs-7 pb-2">Cabang</label>
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

                        <div class="col-lg-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-dark rounded-1 w-100" id="btnView">
                                <i class="la la-search"></i> Lihat Report
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 mb-5 mt-1 border-gray-200 pt-4">
                        <div class="col-lg-8 d-flex align-items-center">
                            <label class="text-dark fw-bold fs-7 pb-2">&nbsp;</label>
                            <label>
                                <input type="radio" class="btn-check" name="byData" value="yoy" checked />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fw-bold px-4">Month ( <i>YoY</i> )</span>
                            </label>

                            <label>
                                <input type="radio" class="btn-check" name="byData" value="ytd" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fw-bold px-4">YTD ( <i>Year to Date</i> )</span>
                            </label>
                        </div>

                        <div class="col-lg-4 d-flex align-items-center justify-content-lg-end">
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-dark cursor-pointer">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="unit_bisnis" id="unit-bisnis" value="t" />
                                <label class="form-check-label text-dark fw-bold fs-7 cursor-pointer" for="unit-bisnis">
                                    Tampilkan Unit Bisnis
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="page-laba-rugi" class="border-top" style="display: none; padding: 20px 24px;">
                <div class="kpi-row">
                    <div class="kpi-card" style="--accent-color:#0FA896">
                        <div class="kpi-label">Total Pendapatan</div>
                        <div class="kpi-value" id="val-pendapatan">0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#3B9B5A">
                        <div class="kpi-label">Laba Bersih</div>
                        <div class="kpi-value" id="val-laba-bersih">0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#E8A820">
                        <div class="kpi-label">EBITDA</div>
                        <div class="kpi-value" id="val-ebitda">0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#E24B4A">
                        <div class="kpi-label">Net Profit Margin</div>
                        <div class="kpi-value" id="val-npm">0 %</div>
                    </div>
                </div>

                <div class="full-card card m-0 per-non-cabang" style="display: none;">
                    <div class="section-header">
                        <div class="section-title">Waterfall Laba Rugi</div>
                    </div>

                    <div id="lr-perbandingan" style="display: block;">
                        <div class="legend-row">
                            <span>
                                <span class="legend-dot" style="background: #0FA896;"></span>
                                <span id="lbl-diff-curr">Bulan/Tahun Terpilih</span>
                            </span>

                            <span>
                                <span class="legend-dot" style="background: #8899AA;"></span>
                                <span id="lbl-diff-prev">Bulan/Tahun Lalu</span>
                            </span>
                        </div>

                        <div class="chart-wrap" style="height: 280px;">
                            <canvas id="lrDifChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="two-col mt-4 per-non-cabang" style="display: none;">
                    <div class="card m-0">
                        <div class="section-header">
                            <div class="section-title">Komposisi Pendapatan</div>
                        </div>

                        <!--<div class="legend-row">
                            <span><span class="legend-dot" style="background: #0FA896;"></span>Rawat Inap</span>
                            <span><span class="legend-dot" style="background: #E8A820;"></span>Rawat Jalan</span>
                            <span><span class="legend-dot" style="background: #0B1A2E;"></span>IGD</span>
                            <span><span class="legend-dot" style="background: #3B9B5A;"></span>Penunjang</span>
                            <span><span class="legend-dot" style="background: #E24B4A;"></span>Lainnya</span>
                        </div>-->

                        <div class="chart-wrap" style="height: 200px;">
                            <canvas id="revPieChart"></canvas>
                        </div>

                        <div id="pie-keterangan" class="mt-5 d-flex flex-column gap-3"></div>
                    </div>

                    <div class="card m-0">
                        <div class="section-header">
                            <div class="section-title">Tren Margin Bulanan</div>
                        </div>

                        <div class="legend-row">
                            <span><span class="legend-dot" style="background: #0FA896;"></span>Gross Margin</span>
                            <span><span class="legend-dot" style="background: #E8A820;"></span>Net Margin</span>
                        </div>

                        <div class="chart-wrap" style="height: 200px;">
                            <canvas id="marginTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="full-card card m-0 per-cabang" style="display: none;">
                    <div class="section-header">
                        <div class="section-title">Revenue Bersih Per Cabang</div>
                    </div>

                    <div id="lr-perbandingan" style="display: block;">
                        <div class="legend-row">
                            <span>
                                <span class="legend-dot" style="background: #0FA896;"></span>
                                <span id="lbl-">Bulan/Tahun Terpilih</span>
                            </span>

                            <span>
                                <span class="legend-dot" style="background: #8899AA;"></span>
                                <span id="lbl-">Bulan/Tahun Lalu</span>
                            </span>
                        </div>

                        <div id="lr-cabang" class="chart-wrap" style="height: 280px;">
                            <canvas id="lrCabangChart"></canvas>
                        </div>
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

        const $form   = $('#form-pld')
        const $sBid   = $form.find('[id="s-Bid"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear  = $form.find('[id="s-Year"]').val()

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

        $btn.html('<i class="las la-spinner la-spin"></i> Memuat Data...').prop('disabled', true)

        $.ajax({
            url         : "{{ route('api.dashboard.income_statement.data') }}",
            type        : "GET",
            data        : {
                            bid: $sBid,
                            month: $sMonth,
                            year: $sYear
                        },
            dataType    : "json",

            success     : function (response)
                        {
                            let $byData = $form.find('input[name="byData"]:checked').val()
                            let $unitBisnis = $form.find('input[id="unit-bisnis"]:checked').val() || 'f'

                            $('#page-laba-rugi').fadeIn()

                            let $tot_income = $laba_bersih = $ebitda = $net_margin = 0

                            $tot_income = response.data_summary.yoy.tot_income
                            $laba_bersih = response.data_summary.yoy.laba_bersih
                            $ebitda = response.data_summary.yoy.ebitda
                            $net_margin = response.data_summary.yoy.net_margin

                            if ($unitBisnis == 't')
                            {
                                $('.per-non-cabang').fadeOut()

                                $('.per-cabang').fadeIn()

                                // 3. Update Chart Laba Bersih Per Cabang
                                // lrCabangChartInstance.data.labels = response.cabang_labels
                                // lrCabangChartInstance.data.datasets[0].data = response.cabang_data
                                // lrCabangChartInstance.update()
                            }
                            else
                            {
                                $('.per-cabang').fadeOut()

                                $('.per-non-cabang').fadeIn()

                                let $diff_prev = $diff_curr = [0,0,0,0,0,0]
                                let $komposisi_data = $komposisi_data_txt = [0,0,0,0,0]

                                if ($byData == 'yoy')
                                {
                                    $diff_prev = [
                                        response.data_diff.yoy.pendapatan_prev,
                                        response.data_diff.yoy.beban_prev,
                                        response.data_diff.yoy.laba_kotor_prev,
                                        response.data_diff.yoy.biaya_umum_prev,
                                        response.data_diff.yoy.ebitda_prev,
                                        response.data_diff.yoy.laba_bersih_prev
                                    ]

                                    $diff_curr = [
                                        response.data_diff.yoy.pendapatan_curr,
                                        response.data_diff.yoy.beban_curr,
                                        response.data_diff.yoy.laba_kotor_curr,
                                        response.data_diff.yoy.biaya_umum_curr,
                                        response.data_diff.yoy.ebitda_curr,
                                        response.data_diff.yoy.laba_bersih_curr
                                    ]

                                    $komposisi_data = [
                                        response.data_komposisi.yoy.ranap,
                                        response.data_komposisi.yoy.rajal,
                                        response.data_komposisi.yoy.igd,
                                        response.data_komposisi.yoy.penunjang,
                                        response.data_komposisi.yoy.lainnya
                                    ]

                                    $komposisi_data_txt = [
                                        response.data_komposisi.yoy.ranap_txt,
                                        response.data_komposisi.yoy.rajal_txt,
                                        response.data_komposisi.yoy.igd_txt,
                                        response.data_komposisi.yoy.penunjang_txt,
                                        response.data_komposisi.yoy.lainnya_txt
                                    ]
                                }
                                else
                                {
                                    $tot_income = response.data_summary.ytd.tot_income
                                    $laba_bersih = response.data_summary.ytd.laba_bersih
                                    $ebitda = response.data_summary.ytd.ebitda
                                    $net_margin = response.data_summary.ytd.net_margin

                                    $diff_prev = [
                                        response.data_diff.ytd.pendapatan_prev,
                                        response.data_diff.ytd.beban_prev,
                                        response.data_diff.ytd.laba_kotor_prev,
                                        response.data_diff.ytd.biaya_umum_prev,
                                        response.data_diff.ytd.ebitda_prev,
                                        response.data_diff.ytd.laba_bersih_prev
                                    ]

                                    $diff_curr = [
                                        response.data_diff.ytd.pendapatan_curr,
                                        response.data_diff.ytd.beban_curr,
                                        response.data_diff.ytd.laba_kotor_curr,
                                        response.data_diff.ytd.biaya_umum_curr,
                                        response.data_diff.ytd.ebitda_curr,
                                        response.data_diff.ytd.laba_bersih_curr
                                    ]

                                    $komposisi_data = [
                                        response.data_komposisi.ytd.ranap,
                                        response.data_komposisi.ytd.rajal,
                                        response.data_komposisi.ytd.igd,
                                        response.data_komposisi.ytd.penunjang,
                                        response.data_komposisi.ytd.lainnya
                                    ]

                                    $komposisi_data_txt = [
                                        response.data_komposisi.ytd.ranap_txt,
                                        response.data_komposisi.ytd.rajal_txt,
                                        response.data_komposisi.ytd.igd_txt,
                                        response.data_komposisi.ytd.penunjang_txt,
                                        response.data_komposisi.ytd.lainnya_txt
                                    ]
                                }
                                
                                $('#val-pendapatan').text($tot_income)
                                $('#val-laba-bersih').text($laba_bersih)
                                $('#val-ebitda').text($ebitda)
                                $('#val-npm').text($net_margin)

                                let lblMonthCurr = response.bulan_curr + ' ' + response.year_curr
                                let lblMonthPrev = response.bulan_prev + ' ' + response.year_curr
                                let lblMonthyearPrev = response.bulan_curr + ' ' + response.year_prev

                                $('#lbl-diff-curr').text(lblMonthCurr)
                                $('#lbl-diff-prev').text(lblMonthyearPrev)

                                lrDiffChartInstance.data.datasets[0].label = lblMonthCurr
                                lrDiffChartInstance.data.datasets[1].label = lblMonthyearPrev

                                lrDiffChartInstance.data.datasets[0].data = $diff_curr
                                lrDiffChartInstance.data.datasets[1].data = $diff_prev

                                lrDiffChartInstance.data.datasets[0].backgroundColor = (context) => {
                                    const value = context.raw || 0
                                    return value < 0 ? '#dc3545' : '#0FA896' 
                                }

                                lrDiffChartInstance.data.datasets[1].backgroundColor = (context) => {
                                    const value = context.raw || 0
                                    return value < 0 ? 'rgba(220, 53, 69, 0.45)' : 'rgba(136, 153, 170, .45)'
                                }

                                lrDiffChartInstance.update()

                                // 4. Update Pie Chart Komposisi
                                revPieChartInstance.data.datasets[0].data = $komposisi_data
                                revPieChartInstance.update()

                                let combinedData = []
                                
                                for (let i = 0; i < pieLabels.length; i++)
                                {
                                    combinedData.push({
                                        label: pieLabels[i],
                                        color: pieColors[i],
                                        nominalTxt: $komposisi_data_txt[i],
                                        rawNominal: $komposisi_data[i] || 0 
                                    })
                                }

                                combinedData.sort((a, b) => b.rawNominal - a.rawNominal)

                                let htmlKeterangan = ''
                                
                                for (let i = 0; i < combinedData.length; i++) {
                                    let item = combinedData[i]

                                    htmlKeterangan += `<div class="d-flex justify-content-between align-items-center fs-7">
                                                            <div class="d-flex align-items-center">
                                                                <span class="legend-dot" style="background: ${item.color}; width: 12px; height: 12px; border-radius: 3px;"></span>
                                                                <span class="text-muted fw-bold ms-2">${item.label}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-dark fw-bolder me-3">${item.nominalTxt}</span>
                                                            </div>
                                                        </div>`
                                }

                                $('#pie-keterangan').html(htmlKeterangan)

                                marginTrendChartInstance.data.labels = [
                                    lblMonthPrev,
                                    lblMonthCurr
                                ];

                                marginTrendChartInstance.data.datasets[0].data = [
                                    response.data_tren_margin.gross_margin_prev,
                                    response.data_tren_margin.gross_margin_curr
                                ]

                                marginTrendChartInstance.data.datasets[1].data = [
                                    response.data_tren_margin.net_margin_prev,
                                    response.data_tren_margin.net_margin_curr
                                ]

                                marginTrendChartInstance.update()
                            }
                        },

            error       : function (xhr)
                        {
                            swalShowMessage('Error!', "Gagal mengambil data dari server.", 'error')
                        },

            complete    : function ()
                        {
                            $btn.html(originalText).prop('disabled', false)
                        }
        })
    })

    // B: Bar Diff
    const gridColor = 'rgba(0,0,0,.06)'
    const txtColor  = '#6A7A88'
    const MONTHS    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']

    const defs = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                grid: { color: gridColor },
                ticks: { color: txtColor, font: { size: 10 } }
            },
            y: {
                grid: { color: gridColor },
                ticks: { color: txtColor, font: { size: 10 } }
            }
        }
    }

    let lrDiffChartInstance = new Chart(document.getElementById('lrDifChart'), {
        type: 'bar',
        data: {
            labels: ['Pendapatan', 'Beban Langsung', 'Laba Kotor', 'Biaya Umum', 'EBITDA', 'Laba Bersih'],
            datasets: [
                {
                    label: 'Terpilih',
                    data: [0,0,0,0,0,0],
                    backgroundColor: '#0FA896',
                    borderRadius: 4
                },
                {
                    label: 'Lalu',
                    data: [0,0,0,0,0,0],
                    backgroundColor: 'rgba(136, 153, 170, .45)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            ...defs, // Mengambil default config Anda
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        // Menonjolkan garis angka 0 agar batas atas/bawah jelas
                        color: (context) => context.tick.value === 0 ? '#555' : 'rgba(0, 0, 0, 0.1)',
                        lineWidth: (context) => context.tick.value === 0 ? 2 : 1
                    }
                },

                x: {
                    ticks: {
                        // Memastikan label teks tetap terbaca meski tertutup batang negatif
                        z: 10 
                    }
                }
            },

            plugins: {
                legend: {
                    display: false
                },

                datalabels: {
                    anchor: (context) => context.dataset.data[context.dataIndex] < 0 ? 'start' : 'end',
                    align: (context) => context.dataset.data[context.dataIndex] < 0 ? 'bottom' : 'top',
                }
            }
        }
    })
    // E: Bar Diff

    // B: Pie Komposisi
    const pieColors = ['#0FA896', '#E8A820', '#0B1A2E', '#3B9B5A', '#E24B4A']
    const pieLabels = ['Rawat Inap', 'Rawat Jalan', 'IGD', 'Penunjang', 'Lainnya']

    let revPieChartInstance = new Chart(document.getElementById('revPieChart'), {
        type: 'doughnut',
        data: {
            labels: pieLabels,
            datasets: [{
                data: [0, 0, 0, 0, 0], // Data awal kosong
                backgroundColor: pieColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Mengatur seberapa besar lubang di tengahnya
            plugins: {
                legend: { display: false }, // Legenda bawaan dimatikan karena pakai HTML
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Menambahkan teks di tooltip
                            let val = context.raw
                            return ' ' + context.label + ': ' + val 
                        }
                    }
                }
            }
        }
    })
    // E: Pie Komposisi

    // B: Chart Tren Margin
    let marginTrendChartInstance = new Chart(document.getElementById('marginTrendChart'), {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [
                {
                    label: 'Gross Margin',
                    data: [],
                    borderColor: '#0FA896',
                    backgroundColor: 'rgba(15,168,150,.08)',
                    fill: true,
                    tension: .35,
                    pointRadius:3,
                    borderWidth:2
                },
                {
                    label: 'Net Margin',
                    data: [],
                    borderColor: '#E8A820',
                    backgroundColor: 'rgba(232,168,32,.06)',
                    fill: true,
                    tension: .35,
                    pointRadius:3,
                    borderWidth:2,
                    borderDash:[5,3]
                }
            ]
        },
        options:{
            ...defs, // Ambil base config
            scales: {
                x: {
                    grid: {
                        display: false // Hilangkan garis vertikal
                    },
                    border: {
                        display: false // Hilangkan garis pinggir sumbu X
                    }
                },
                y: {
                    grid: {
                        display: false // INI UNTUK HILANGKAN GARIS KE SAMPING
                    },
                    border: {
                        display: false // Hilangkan garis pinggir sumbu Y
                    },
                    ticks: {
                        // Jika ingin angka di samping tetap ada, jangan hapus ticks
                        color: '#6A7A88',
                        font: { size: 10 }
                    }
                }
            }
        }
    })
    // E: Chart Tren Margin
</script>
@endpush