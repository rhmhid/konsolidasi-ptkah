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
                            <h1 class="pt-2 text-dark">
                                <span class="las la-chart-pie text-dark me-3 fs-1"></span> 
                                <span class="text-dark fs-1">Income Statement</span> 
                                <span class="text-muted fw-normal ms-2 fs-3">| Dashboard Eksekutif</span>
                            </h1>
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
                                <i class="la la-search"></i> Analisis Data
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 mb-5 mt-1 border-gray-200 pt-4">
                        <div class="col-lg-8 d-flex align-items-center">
                            <label class="text-dark fw-bold fs-7 pb-2">&nbsp;</label>
                            <label>
                                <input type="radio" class="btn-check" name="byData" value="yoy" checked />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fw-bold px-4">Kinerja Bulanan ( <i>YoY</i> )</span>
                            </label>

                            <label>
                                <input type="radio" class="btn-check" name="byData" value="ytd" />
                                <span class="btn btn-sm btn-color-muted btn-active btn-active-dark fw-bold px-4">Kinerja Akumulatif ( <i>YTD</i> )</span>
                            </label>
                        </div>

                        <div class="col-lg-4 d-flex align-items-center justify-content-lg-end">
                            <div class="form-check form-switch form-check-custom form-check-solid form-check-dark cursor-pointer">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="unit_bisnis" id="unit-bisnis" value="t" />
                                <label class="form-check-label text-dark fw-bold fs-7 cursor-pointer" for="unit-bisnis">
                                    Konsolidasi Unit Bisnis
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="page-laba-rugi" class="border-top" style="padding: 20px 24px;">
                <div class="kpi-row">
                    <div class="kpi-card" style="--accent-color:#0FA896">
                        <div class="kpi-label">Total Pendapatan</div>
                        <div class="kpi-value" id="val-pendapatan">Rp. 0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#3B9B5A">
                        <div class="kpi-label">Laba Bersih</div>
                        <div class="kpi-value" id="val-laba-bersih">Rp. 0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#E8A820">
                        <div class="kpi-label">EBITDA</div>
                        <div class="kpi-value" id="val-ebitda">Rp. 0</div>
                    </div>

                    <div class="kpi-card" style="--accent-color:#E24B4A">
                        <div class="kpi-label">Net Profit Margin</div>
                        <div class="kpi-value" id="val-npm">0 %</div>
                    </div>
                </div>

                <div class="full-card card m-0 per-non-cabang">
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

                <div class="two-col mt-4 per-non-cabang">
                    <div class="card m-0">
                        <div class="section-header">
                            <div class="section-title">Komposisi Pendapatan</div>
                        </div>

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

                <div class="row g-4 per-cabang">
                    <div class="col-lg-7">
                        <div class="card h-100 m-0">
                            <div class="section-header d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="section-title">Revenue Bersih per Cabang</div>
                                    <div class="text-muted fs-8 mt-1">&nbsp;</div>
                                </div>
                            </div>

                            <div id="lr-cabang" class="chart-wrap" style="height: 320px;">
                                <canvas id="lrCabangChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card h-100 m-0">
                            <div class="section-header">
                                <div>
                                    <div class="section-title">Komposisi Revenue</div>
                                    <div class="text-muted fs-8 mt-1">Proporsi per entitas (%)</div>
                                </div>
                            </div>
                            
                            <div class="chart-wrap mt-3" style="height: 220px;">
                                <canvas id="pieCabangChart"></canvas>
                            </div>

                            <div id="pie-cabang-keterangan" class="mt-5 d-flex flex-column"></div>
                        </div>
                    </div>
                </div>

                <div class="card m-0 mt-4 per-cabang" style="display: none;">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="section-title">Detail Pendapatan per Cabang (Rp)</div>
                            <div class="text-muted fs-8 mt-1">Rincian komponen pendapatan operasional</div>
                        </div>
                        <div class="badge px-3 py-2" style="background: #E1EFFE; color: #1D4ED8;" id="badge-entitas">0 Entitas</div>
                    </div>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="border-0 text-muted fw-bolder text-uppercase fs-8" style="letter-spacing: 0.5px;">
                                    <th class="ps-0">Entitas</th>
                                    <th class="text-end">IGD</th>
                                    <th class="text-end">Rawat Inap</th>
                                    <th class="text-end">Rawat Jalan</th>
                                    <th class="text-end">Penunjang Medik</th>
                                    <th class="text-end">Lainnya</th>
                                    <th class="text-end">Total Bruto</th>
                                    <th class="text-end">Pengurangan</th>
                                    <th class="text-end">Non Operasional</th>
                                    <th class="text-end">Total Bersih</th>
                                    <th class="text-end pe-0">Share %</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-detail-cabang">
                            </tbody>
                            <tfoot>
                                <tr class="fw-bolder fs-7 bg-light">
                                    <td class="ps-3 rounded-start text-dark">Total Konsolidasi</td>
                                    <td class="text-end text-dark" id="tf-igd">0</td>
                                    <td class="text-end text-dark" id="tf-ranap">0</td>
                                    <td class="text-end text-dark" id="tf-rajal">0</td>
                                    <td class="text-end text-dark" id="tf-penunjang">0</td>
                                    <td class="text-end text-dark" id="tf-lainnya">0</td>
                                    <td class="text-end text-dark" id="tf-bruto">0</td>
                                    <td class="text-end" id="tf-pengurangan">0</td> <td class="text-end text-dark" id="tf-non-operasional">0</td>
                                    <td class="text-end text-success" id="tf-bersih">0</td>
                                    <td class="text-end pe-3 rounded-end">
                                        <span class="badge bg-light-success text-success fw-bolder px-2 py-1">100%</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card m-0 mt-4 per-cabang" style="display: none;">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="section-title">P&L Summary per Cabang (Rp)</div>
                        </div>
                    </div>
                    
                    <div class="table-responsive mt-3">
                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="border-0 text-muted fw-bolder text-uppercase fs-8" style="letter-spacing: 0.5px;">
                                    <th class="ps-0">Entitas</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Biaya Langsung</th>
                                    <th class="text-end">Laba Kotor</th>
                                    <th class="text-end">GPM%</th>
                                    <th class="text-end">Biaya Umum</th>
                                    <th class="text-end">EBITDA</th>
                                    <th class="text-end">EBITDA%</th>
                                    <th class="text-end">EBIT</th>
                                    <th class="text-end">EAT</th>
                                    <th class="text-end pe-0">NPM%</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-pl-cabang">
                            </tbody>
                            <tfoot>
                                <tr class="fw-bolder fs-7 bg-light">
                                    <td class="ps-3 rounded-start text-dark">Total Konsolidasi</td>
                                    <td class="text-end text-dark" id="tf-pl-rev">0</td>
                                    <td class="text-end" id="tf-pl-bl">0</td> <td class="text-end" id="tf-pl-lk">0</td>
                                    <td class="text-end" id="tf-pl-gpm">0%</td>
                                    <td class="text-end" id="tf-pl-opex">0</td> <td class="text-end" id="tf-pl-ebitda">0</td>
                                    <td class="text-end" id="tf-pl-ebitdapct">0%</td>
                                    <td class="text-end" id="tf-pl-ebit">0</td>
                                    <td class="text-end" id="tf-pl-eat">0</td>
                                    <td class="text-end pe-3 rounded-end" id="tf-pl-npm">0%</td>
                                </tr>
                            </tfoot>
                        </table>
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

        $btn.html('<i class="las la-spinner la-spin"></i> Memuat...').prop('disabled', true)

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

                            if ($byData == 'yoy')
                            {
                                $tot_income = response.data_summary.yoy.tot_income
                                $laba_bersih = response.data_summary.yoy.laba_bersih
                                $ebitda = response.data_summary.yoy.ebitda
                                $net_margin = response.data_summary.yoy.net_margin
                            }
                            else
                            {
                                $tot_income = response.data_summary.ytd.tot_income
                                $laba_bersih = response.data_summary.ytd.laba_bersih
                                $ebitda = response.data_summary.ytd.ebitda
                                $net_margin = response.data_summary.ytd.net_margin
                            }

                            $('#val-pendapatan').text($tot_income)
                            $('#val-laba-bersih').text($laba_bersih)
                            $('#val-ebitda').text($ebitda)
                            $('#val-npm').text($net_margin)

                            if ($unitBisnis == 't')
                            {
                                $('.per-non-cabang').fadeOut()
                                $('.per-cabang').fadeIn()

                                let $dataCabang = []
                                let $revenueCabangName = $revenueCabangAmount = $revenueCabangWarna = []

                                if ($byData == 'yoy')
                                {
                                    $dataCabang = response.data_revenue_cabang.yoy
                                    $revenueCabangName = response.data_revenue_cabang.yoy.cabang
                                    $revenueCabangAmount = response.data_revenue_cabang.yoy.amount
                                    $revenueCabangWarna = response.data_revenue_cabang.yoy.warna
                                }
                                else
                                {
                                    $dataCabang = response.data_revenue_cabang.ytd
                                    $revenueCabangName = response.data_revenue_cabang.ytd.cabang
                                    $revenueCabangAmount = response.data_revenue_cabang.ytd.amount
                                    $revenueCabangWarna = response.data_revenue_cabang.ytd.warna
                                }

                                lrCabangChartInstance.data.labels = $revenueCabangName
                                lrCabangChartInstance.data.datasets[0].data = $revenueCabangAmount
                                lrCabangChartInstance.data.datasets[0].backgroundColor = $revenueCabangWarna
                                lrCabangChartInstance.update()

                                pieCabangChartInstance.data.labels = $revenueCabangName
                                pieCabangChartInstance.data.datasets[0].data = $revenueCabangAmount
                                pieCabangChartInstance.data.datasets[0].backgroundColor = $revenueCabangWarna
                                pieCabangChartInstance.update()

                                let totalAmount = $dataCabang.amount.reduce((a, b) => a + parseFloat(b || 0), 0)
                                let combinedData = []

                                for (let i = 0; i < $dataCabang.cabang.length; i++)
                                {
                                    let val = parseFloat($dataCabang.amount[i]) || 0
                                    let pct = totalAmount > 0 ? ((val / totalAmount) * 100).toFixed(1) : 0
                                    
                                    combinedData.push({
                                        label: $dataCabang.cabang[i],
                                        color: $dataCabang.warna[i],
                                        detail: $dataCabang.detail[i],
                                        rawVal: val,
                                        fmtVal: formatKeJT(val),
                                        pct: pct
                                    })
                                }

                                // Urutkan dari yang pendapatannya paling besar
                                combinedData.sort((a, b) => b.rawVal - a.rawVal)

                                let htmlLegenda = ''
                                combinedData.forEach(item => {
                                    htmlLegenda += `<div class="d-flex justify-content-between align-items-center py-3 border-bottom border-gray-200">
                                                        <div class="d-flex align-items-center">
                                                            <span class="legend-dot" style="background: ${item.color}; width: 12px; height: 12px; border-radius: 50%;"></span>
                                                            <span class="text-dark fw-bold ms-3 fs-7">${item.label}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="text-dark fw-bolder fs-7">${item.fmtVal}</span>
                                                            <span class="badge fw-bolder fs-8 px-2 py-1" style="background: #E1EFFE; color: #1D4ED8; min-width: 45px;">${item.pct}%</span>
                                                        </div>
                                                    </div>`
                                })

                                $('#pie-cabang-keterangan').html(htmlLegenda)
                                $('#badge-entitas').text(combinedData.length + ' Entitas')

                                // ==========================================
                                // Helper Fungsi Khusus Biaya (Positif=Hijau, Negatif=Merah & Tanda Kurung)
                                // ==========================================
                                const formatKolomKhusus = (val) => {
                                    let num = parseFloat(val) || 0
                                    if (num > 0) return `<span class="text-success">${MoneyFormat(num)}</span>`
                                    if (num < 0) return `<span class="text-danger">(${MoneyFormat(Math.abs(num))})</span>`
                                    return '0'
                                }

                                let htmlTabel = ''
                                let sumIgd = 0, sumRanap = 0, sumRajal = 0, sumPenunjang = 0, sumLainnya = 0
                                let sumBruto = 0, sumPengurangan = 0, sumNonOp = 0, sumBersih = 0

                                let htmlTabelPL = ''
                                let sumPlRev = 0, sumPlbl = 0, sumPlLk = 0, sumPlOpex = 0, sumPlEbitda = 0, sumPlEbit = 0, sumPlEat = 0

                                combinedData.forEach(item => {
                                    if (item.detail)
                                    {
                                        // --- TABEL 1: DETAIL PENDAPATAN ---
                                        sumIgd += parseFloat(item.detail.igd) || 0
                                        sumRanap += parseFloat(item.detail.ranap) || 0
                                        sumRajal += parseFloat(item.detail.rajal) || 0
                                        sumPenunjang += parseFloat(item.detail.penunjang) || 0
                                        sumLainnya += parseFloat(item.detail.lainnya) || 0
                                        sumBruto += parseFloat(item.detail.bruto) || 0
                                        sumPengurangan += parseFloat(item.detail.pengurangan) || 0
                                        sumNonOp += parseFloat(item.detail.non_operasional) || 0
                                        sumBersih += parseFloat(item.detail.bersih) || 0

                                        htmlTabel += `<tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="legend-dot" style="background: ${item.color}; width: 10px; height: 10px; border-radius: 50%;"></span>
                                                                <span class="text-dark fs-7 ms-3">${item.label}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-end text-dark fs-7">${item.detail.igd == 0 ? '0' : MoneyFormat(item.detail.igd)}</td>
                                                        <td class="text-end text-dark fs-7">${item.detail.ranap == 0 ? '0' : MoneyFormat(item.detail.ranap)}</td>
                                                        <td class="text-end text-dark fs-7">${item.detail.rajal == 0 ? '0' : MoneyFormat(item.detail.rajal)}</td>
                                                        <td class="text-end text-dark fs-7">${item.detail.penunjang == 0 ? '0' : MoneyFormat(item.detail.penunjang)}</td>
                                                        <td class="text-end text-dark fs-7">${item.detail.lainnya == 0 ? '0' : MoneyFormat(item.detail.lainnya)}</td>
                                                        <td class="text-end text-dark fw-bold fs-7">${MoneyFormat(item.detail.bruto)}</td>
                                                        <td class="text-end fs-7">${formatKolomKhusus(item.detail.pengurangan)}</td>
                                                        <td class="text-end text-dark fs-7">${item.detail.non_operasional == 0 ? '0' : MoneyFormat(item.detail.non_operasional)}</td>
                                                        <td class="text-end text-success fw-bold fs-7">${MoneyFormat(item.detail.bersih)}</td>
                                                        <td class="text-end pe-0">
                                                            <span class="badge fw-bolder fs-8 px-2 py-1" style="background: #E1EFFE; color: #1D4ED8;">${item.pct}%</span>
                                                        </td>
                                                    </tr>`

                                        // --- TABEL 2: P&L SUMMARY ---
                                        let rev = parseFloat(item.detail.bersih) || 0
                                        let bl = parseFloat(item.detail.langsung) || 0
                                        let lk = parseFloat(item.detail.kotor) || 0
                                        let opex = parseFloat(item.detail.opex) || 0
                                        let ebitda = parseFloat(item.detail.ebitda) || 0
                                        let ebit = parseFloat(item.detail.ebit) || 0
                                        let eat = parseFloat(item.detail.eat) || 0

                                        sumPlRev += rev
                                        sumPlbl += bl
                                        sumPlLk += lk
                                        sumPlOpex += opex
                                        sumPlEbitda += ebitda
                                        sumPlEbit += ebit
                                        sumPlEat += eat

                                        let gpm = rev !== 0 ? ((lk / rev) * 100).toFixed(1) : 0
                                        let ebitdapct = rev !== 0 ? ((ebitda / rev) * 100).toFixed(1) : 0
                                        let npm = rev !== 0 ? ((eat / rev) * 100).toFixed(1) : 0

                                        const formatWarna = (val) => {
                                            if (val > 0) return `<span class="text-success fw-bold">${MoneyFormat(val)}</span>`
                                            if (val < 0) return `<span class="text-danger fw-bold">(${MoneyFormat(Math.abs(val))})</span>`
                                            return `<span class="text-dark fw-bold">0</span>`
                                        }

                                        const badgePct = (val) => {
                                            if (val > 0) return `<span class="badge bg-light-success text-success fw-bolder px-2 py-1">${val}%</span>`
                                            if (val < 0) return `<span class="badge bg-light-danger text-danger fw-bolder px-2 py-1">${val}%</span>`
                                            return `<span class="badge bg-light-primary text-primary fw-bolder px-2 py-1">${val}%</span>`
                                        }

                                        htmlTabelPL += `<tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="legend-dot" style="background: ${item.color}; width: 10px; height: 10px; border-radius: 50%;"></span>
                                                                    <span class="text-dark fs-7 ms-3">${item.label}</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end text-dark fw-bold fs-7">${rev == 0 ? '0' : MoneyFormat(rev)}</td>
                                                            <td class="text-end fs-7">${formatKolomKhusus(bl)}</td>
                                                            <td class="text-end fs-7">${formatWarna(lk)}</td>
                                                            <td class="text-end pe-0">${badgePct(gpm)}</td>
                                                            <td class="text-end fs-7">${formatKolomKhusus(opex)}</td>
                                                            <td class="text-end fs-7">${formatWarna(ebitda)}</td>
                                                            <td class="text-end pe-0">${badgePct(ebitdapct)}</td>
                                                            <td class="text-end fs-7">${formatWarna(ebit)}</td>
                                                            <td class="text-end fs-7">${formatWarna(eat)}</td>
                                                            <td class="text-end pe-0">${badgePct(npm)}</td>
                                                        </tr>`
                                    }
                                }) // Akhir Loop

                                // --- TEMPEL DATA TABEL 1 ---
                                $('#tbody-detail-cabang').html(htmlTabel)
                                $('#tf-igd').text(MoneyFormat(sumIgd))
                                $('#tf-ranap').text(MoneyFormat(sumRanap))
                                $('#tf-rajal').text(MoneyFormat(sumRajal))
                                $('#tf-penunjang').text(MoneyFormat(sumPenunjang))
                                $('#tf-lainnya').text(MoneyFormat(sumLainnya))
                                $('#tf-bruto').text(MoneyFormat(sumBruto))
                                $('#tf-non-operasional').text(MoneyFormat(sumNonOp))
                                $('#tf-bersih').text(MoneyFormat(sumBersih))
                                $('#tf-pengurangan').html(formatKolomKhusus(sumPengurangan))

                                // --- TEMPEL DATA TABEL 2 ---
                                $('#tbody-pl-cabang').html(htmlTabelPL)
                                $('#tf-pl-rev').text(MoneyFormat(sumPlRev))
                                $('#tf-pl-bl').html(formatKolomKhusus(sumPlbl))
                                $('#tf-pl-opex').html(formatKolomKhusus(sumPlOpex))

                                $('#tf-pl-lk').html(sumPlLk < 0 ? `<span class="text-danger fw-bold">(${MoneyFormat(Math.abs(sumPlLk))})</span>` : `<span class="text-success fw-bold">${MoneyFormat(sumPlLk)}</span>`)
                                
                                let totalGpm = sumPlRev !== 0 ? ((sumPlLk / sumPlRev) * 100).toFixed(1) : 0
                                $('#tf-pl-gpm').html(totalGpm < 0 ? `<span class="text-danger fw-bolder">${totalGpm}%</span>` : `<span class="text-success fw-bolder">${totalGpm}%</span>`)
                                
                                $('#tf-pl-ebitda').html(sumPlEbitda < 0 ? `<span class="text-danger fw-bold">(${MoneyFormat(Math.abs(sumPlEbitda))})</span>` : `<span class="text-success fw-bold">${MoneyFormat(sumPlEbitda)}</span>`)
                                
                                let totalEbitdaPct = sumPlRev !== 0 ? ((sumPlEbitda / sumPlRev) * 100).toFixed(1) : 0
                                $('#tf-pl-ebitdapct').html(totalEbitdaPct < 0 ? `<span class="text-danger fw-bolder">${totalEbitdaPct}%</span>` : `<span class="text-success fw-bolder">${totalEbitdaPct}%</span>`)
                                
                                $('#tf-pl-ebit').html(sumPlEbit < 0 ? `<span class="text-danger fw-bold">(${MoneyFormat(Math.abs(sumPlEbit))})</span>` : `<span class="text-success fw-bold">${MoneyFormat(sumPlEbit)}</span>`)
                                
                                $('#tf-pl-eat').html(sumPlEat < 0 ? `<span class="text-danger fw-bold">(${MoneyFormat(Math.abs(sumPlEat))})</span>` : `<span class="text-success fw-bold">${MoneyFormat(sumPlEat)}</span>`)
                                
                                let totalNpm = sumPlRev !== 0 ? ((sumPlEat / sumPlRev) * 100).toFixed(1) : 0
                                $('#tf-pl-npm').html(totalNpm < 0 ? `<span class="text-danger fw-bolder">${totalNpm}%</span>` : `<span class="text-success fw-bolder">${totalNpm}%</span>`)
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

                                // Update Pie Chart Komposisi
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

                                    htmlKeterangan += `<div class="d-flex justify-content-between align-items-center fs-7 border-bottom border-gray-200">
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
                                ]

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
            ...defs,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: (context) => context.tick.value === 0 ? '#555' : 'rgba(0, 0, 0, 0.1)',
                        lineWidth: (context) => context.tick.value === 0 ? 2 : 1
                    }
                },
                x: {
                    ticks: {
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
                data: [0, 0, 0, 0, 0],
                backgroundColor: pieColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
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
                    pointRadius: 3,
                    borderWidth: 2,
                    pointBackgroundColor: '#0FA896'
                },
                {
                    label: 'Net Margin',
                    data: [],
                    borderColor: '#E8A820',
                    backgroundColor: 'rgba(232,168,32,.06)',
                    fill: true,
                    tension: .35,
                    pointRadius: 3,
                    borderWidth: 2,
                    borderDash: [5,3],
                    pointBackgroundColor: '#E8A820'
                }
            ]
        },
        options:{
            ...defs,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.raw + '%'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#6A7A88', font: { size: 10 }, callback: v => v + '%' }
                }
            }
        }
    })
    // E: Chart Tren Margin

    // B: Chart Revenue Per Cabang
    let lrCabangChartInstance = new Chart(document.getElementById('lrCabangChart'), {
        type: 'bar',
        data: {
            labels: [], 
            datasets: [{
                label: 'Revenue Per Cabang',
                data: [], 
                backgroundColor: [], 
                borderRadius: 6,
                borderSkipped: false,
                maxBarThickness: 35
            }]
        },
        options: {
            ...defs,
            layout: {
                padding: { top: 25 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let rawValue = context.raw || 0
                            return ' ' + formatKeJT(rawValue) 
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: txtColor,
                    font: { size: 9, weight: '600' },
                    formatter: function(value) {
                        return formatKeJT(value)
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: txtColor, font: { size: 11 } }
                },
                y: {
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: '15%',
                    ticks: { 
                        color: txtColor, 
                        font: { size: 10 },
                        callback: function (value) {
                            return formatKeJT(value)
                        }
                    }
                }
            }
        }
    })
    // E: Chart Revenue Per Cabang

    // B: Chart Pie Cabang
    let pieCabangChartInstance = new Chart(document.getElementById('pieCabangChart'), {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [], 
                backgroundColor: [],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let rawValue = context.raw || 0;
                            return ' ' + context.label + ': ' + formatKeJT(rawValue); 
                        }
                    }
                }
            }
        }
    });
    // E: Chart Pie Cabang
</script>
@endpush