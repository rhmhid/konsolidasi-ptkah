@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --blue-main: #1d70b8;
            --blue-light: #e9f2f9;
            --muted-blue: #aab8c2;
            --muted-red: #e57373;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --bg-dash: #f8fafc;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.05);
            --border-color: #edf2f7;
        }

        .container-dash { 
            max-width: 100%; 
            padding: 25px; 
            background: var(--bg-dash);
        }

        /* Section Title Custom */
        .section-header-dash { 
            font-size: 11px; 
            font-weight: 800; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            margin: 30px 0 15px; 
            letter-spacing: 1.2px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-header-dash::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        /* Metric Grids */
        .metric-grid { 
            display: grid; 
            gap: 20px; 
            margin-bottom: 10px; 
        }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }

        .metric-card { 
            background: white; 
            border-radius: 12px; 
            padding: 20px; 
            box-shadow: var(--card-shadow); 
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease;
        }
        .metric-card:hover { transform: translateY(-3px); }

        .m-val { 
            font-size: 28px; 
            font-weight: 700; 
            color: var(--blue-main); 
            margin-bottom: 2px;
        }
        .m-title { 
            font-size: 12px; 
            font-weight: 600; 
            text-transform: uppercase; 
            color: var(--text-muted); 
            margin-bottom: 15px; 
        }
        .m-chart { height: 70px; margin-bottom: 15px; }
        
        .m-footer { 
            display: flex; 
            justify-content: space-between; 
            font-size: 11px; 
            color: var(--text-muted); 
            border-top: 1px solid var(--border-color); 
            padding-top: 12px; 
        }
        .m-avg { 
            background: var(--blue-light); 
            color: var(--blue-main); 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-weight: 700; 
        }

        /* Large Cards */
        .card-large { 
            background: white; 
            border-radius: 12px; 
            padding: 25px; 
            box-shadow: var(--card-shadow); 
            margin-top: 25px;
            border: 1px solid var(--border-color);
        }
        .card-large h2 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Waterfall Layout */
        .wf-layout { display: flex; flex-wrap: wrap; gap: 30px; }
        .wf-table { flex: 1; min-width: 350px; }
        .wf-chart { flex: 2; min-width: 450px; }
        
        .wf-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 10px; 
            font-size: 13px; 
            border-bottom: 1px solid #f8fafc;
            border-radius: 6px;
            transition: background 0.2s;
            position: relative;
            cursor: pointer;
        }
        .wf-row:hover { background: #f1f5f9; }
        .wf-row.total-row { background: #f0f4f8; font-weight: bold; margin-top: 10px; border-bottom: none; }
        
        .val-p { font-weight: 700; color: var(--blue-main); }
        .val-n { font-weight: 700; color: #e57373; }

        .wf-tooltip {
            visibility: hidden;
            width: 220px;
            background: #2d3436;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            position: absolute;
            z-index: 100;
            bottom: 110%;
            left: 20px;
            font-size: 11px;
            line-height: 1.4;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .wf-row:hover .wf-tooltip { visibility: visible; opacity: 1; }

        #sankey-chart svg { font-family: inherit; }
    </style>

<div class="d-flex flex-column flex-lg-row">
    <div class="flex-lg-row-fluid">
        <div class="card border border-gray-300 rounded-1">
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-chart-bar text-primary me-4"></span>
                                Performance Dashboard (Income Statement)
                            </h2>
                        </div>
                    </div>
                </div>

                <form method="post" id="form-pl" class="p-6 pb-0">
                    <div class="row g-4 mb-5">
                        <div class="col-lg-4">
                            <label class="text-dark fw-bold fs-7 pb-2">Cabang</label>
                            {!! $cmb_cabang !!}
                        </div>
                        <div class="col-lg-3">
                            <label class="text-dark fw-bold fs-7 pb-2">Bulan</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Month" data-control="select2">
                                {!! get_combo_option_month_lk(date('m')) !!}
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="text-dark fw-bold fs-7 pb-2">Tahun</label>
                            <select class="form-select form-select-sm rounded-1 w-100" id="s-Year" data-control="select2">
                                {!! get_combo_option_year(date('Y'), 2024, date('Y')+1) !!}
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

            <div class="container-dash">
                <div class="section-header-dash">Core Financials (Billion IDR)</div>
                <div class="metric-grid grid-3" id="core-container"></div>

                <div class="section-header-dash">Margin Analysis (%)</div>
                <div class="metric-grid grid-4" id="margin-container"></div>

                <div class="card-large">
                    <h2><i class="las la-project-diagram text-primary"></i> Financial Flow (Sankey)</h2>
                    <div id="sankey-chart" style="height: 380px;"></div>
                </div>

                <div class="card-large">
                    <h2><i class="las la-stream text-primary"></i> Earnings Waterfall Analysis</h2>
                    <div class="wf-layout">
                        <div class="wf-table">
                            <div class="wf-row"><span>Revenue</span><span class="val-p">1,552.6B</span></div>
                            <div class="wf-row" style="padding-left:30px; color:var(--text-muted)"><span>Cost of Revenue</span><span class="val-n">-852.4B</span></div>
                            <div class="wf-row"><span>Gross Profit</span><span class="val-p">700.2B</span></div>
                            <div class="wf-row" style="padding-left:30px; color:var(--text-muted)"><span>Operating Expenses</span><span class="val-n">-616.2B</span></div>
                            <div class="wf-row"><span>Operating Income</span><span class="val-p">84.0B</span></div>
                            <div class="wf-row">
                                <span>Other Expenses</span><span class="val-n">-71.6B</span>
                                <div class="wf-tooltip">Beban non-operasional seperti pembayaran bunga, pajak, serta biaya tidak rutin lainnya.</div>
                            </div>
                            <div class="wf-row total-row"><span>Net Income</span><span class="val-p">12.4B</span></div>
                        </div>
                        <div class="wf-chart" id="wf-apex"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    // Filter Action
    $('#btnView').click(function (e) {
        e.preventDefault();
        const $form = $('#form-pl')
        const $sBid = $form.find('[id="s-Bid"]').val()
        const $sMonth = $form.find('[id="s-Month"]').val()
        const $sYear = $form.find('[id="s-Year"]').val()

        if ($sBid == '')
        {
            swalShowMessage('Perhatian!', "Cabang Harus Dipilih.", 'warning')

            return false
        }

            var $param = 'bid=' + $sBid
                $param += '&month=' + $sMonth
                $param += '&year=' + $sYear

        let $link = "{{ route('dashboard.income_statement', []) }}"

        window.location.replace($link + '?' + $param)
        return false



    });

    // 1. Data Config
    const coreMetrics = [
        {t: 'Revenue', v: '1,552.6B', d: [1100, 1150, 1200, 1350, 1400, 1450, 1552.6], avg: '-4%'},
        {t: 'Operating Income', v: '84.0B', d: [200, 180, 150, 120, 100, 90, 84], avg: '-59%'},
        {t: 'Net Income', v: '12.4B', d: [150, 120, 100, 80, 50, 20, 12.4], avg: '-87%'}
    ];

    const marginMetrics = [
        {t: 'Gross Margin', v: '45.1%', d: [40,42,45,44,46,45,47,46,45,45,45.1], avg: '46%'},
        {t: 'Operating Margin', v: '5.4%', d: [15,14,12,10,8,7,6,5.5,5,5.2,5.4], avg: '7%'},
        {t: 'Net Margin', v: '0.8%', d: [8,7,6,5,4,3,2,1,0.5,0.7,0.8], avg: '1%'},
        {t: 'FCF Margin', v: '-5.6%', d: [2,-1,-3,-5,-8,-7,-6,-5,-4,-5,-5.6], avg: '-8%', neg:true}
    ];

    // 2. Render Cards
    function renderCards(targetId, dataArray, prefix) {
        const target = document.getElementById(targetId);
        dataArray.forEach((m, i) => {
            target.innerHTML += `
                <div class="metric-card">
                    <div class="m-val" style="color: ${m.neg ? '#e57373' : 'var(--blue-main)'}">${m.v}</div>
                    <div class="m-title">${m.t}</div>
                    <div class="m-chart"><canvas id="${prefix}Chart${i}"></canvas></div>
                    <div class="m-footer">
                        <span>3Y Avg <span class="m-avg" style="${m.neg ? 'background:#feebeb; color:#c62828' : ''}">${m.avg}</span></span>
                        <span>5Y Avg <span class="m-avg">5%</span></span>
                    </div>
                </div>
            `;
        });
    }

    // 3. Render Sparkline Charts
    function renderCharts(dataArray, prefix) {
        dataArray.forEach((m, i) => {
            new Chart(document.getElementById(`${prefix}Chart${i}`).getContext('2d'), {
                type: 'bar',
                data: { 
                    labels: m.d.map(x=>''), 
                    datasets: [{ 
                        data: m.d, 
                        backgroundColor: m.neg ? '#e57373' : (prefix === 'core' ? '#1d70b8' : '#00a896'), 
                        borderRadius: 3, 
                        barThickness: 8 
                    }]
                },
                options: { 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } }, 
                    scales: { x: { display: false }, y: { display: false } } 
                }
            });
        });
    }

    // 4. Sankey Chart Logic
    const drawSankey = () => {
        const target = document.getElementById("sankey-chart");
        const width = target.offsetWidth, height = 350;
        const svg = d3.select("#sankey-chart").append("svg")
            .attr("width", "100%")
            .attr("height", height)
            .append("g");

        const sankey = d3.sankey()
            .nodeWidth(24)
            .nodePadding(35)
            .extent([[5, 5], [width - 5, height - 5]]);

        const data = {
            nodes: [{name: "Revenue"}, {name: "Gross Profit"}, {name: "Cost of Revenue"}, {name: "Net Income"}, {name: "Expenses"}, {name: "SG&A"}, {name: "Other"}],
            links: [
                {source: 0, target: 1, value: 700.3}, {source: 0, target: 2, value: 852.7},
                {source: 1, target: 3, value: 12.4}, {source: 1, target: 4, value: 687.9},
                {source: 4, target: 5, value: 499.4}, {source: 4, target: 6, value: 188.5}
            ]
        };

        const {nodes, links} = sankey(data); 
        
        svg.append("g").selectAll("path")
            .data(links).enter().append("path")
            .attr("d", d3.sankeyLinkHorizontal())
            .attr("fill", "none")
            .attr("stroke", d => d.target.name.includes("Income") ? "#aab8c2" : "#f8d7da")
            .attr("stroke-opacity", 0.5)
            .attr("stroke-width", d => Math.max(1, d.width));

        const node = svg.append("g").selectAll("g").data(nodes).enter().append("g");

        node.append("rect")
            .attr("x", d => d.x0).attr("y", d => d.y0)
            .attr("height", d => d.y1 - d.y0).attr("width", 24)
            .attr("fill", d => d.name === "Revenue" ? "var(--blue-main)" : "#cfd8dc")
            .attr("rx", 4);

        node.append("text")
            .attr("x", d => d.x0 - 12).attr("y", d => (d.y1 + d.y0) / 2)
            .attr("dy", "0.35em").attr("text-anchor", "end")
            .style("font-size", "12px").style("font-weight", "500")
            .text(d => d.name)
            .filter(d => d.x0 < width / 2)
            .attr("x", d => d.x1 + 12).attr("text-anchor", "start");
    };

    // Initialize All
    renderCards('core-container', coreMetrics, 'core');
    renderCards('margin-container', marginMetrics, 'margin');
    renderCharts(coreMetrics, 'core');
    renderCharts(marginMetrics, 'margin');
    drawSankey();

    // Waterfall Apex
    new ApexCharts(document.querySelector("#wf-apex"), {
        series: [{ data: [
            { x: 'Revenue', y: [0, 1553], color: '#aab8c2' },
            { x: 'Cost of Revenue', y: [700, 1553], color: '#e58a8a' },
            { x: 'Gross Profit', y: [0, 700], color: '#74b9ff' },
            { x: 'OpEx', y: [84, 700], color: '#e58a8a' },
            { x: 'Op Income', y: [0, 84], color: '#81ecec' },
            { x: 'Other', y: [12, 84], color: '#fab1a0' },
            { x: 'Net Income', y: [0, 12], color: 'var(--blue-main)' }
        ]}],
        chart: { type: 'rangeBar', height: 350, toolbar: {show:false} },
        plotOptions: { bar: { columnWidth: '60%', borderRadius: 4 } },
        dataLabels: { enabled: true, formatter: (val, opt) => {
            let diff = Math.abs(val[1] - val[0]);
            return diff + 'B';
        }},
        yaxis: { show: false },
        grid: { strokeDashArray: 4, padding: { left: 20, right: 20 } }
    }).render();

</script>
@endpush