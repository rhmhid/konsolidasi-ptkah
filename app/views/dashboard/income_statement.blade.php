@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        :root {
            --blue-main: #1d70b8;
            --muted-blue: #aab8c2;
            --muted-red: #e58a8a;
            --bg-body: #f4f7f9;
            --card-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-body); margin: 0; padding: 30px; color: #333; }
        .container { max-width: 1300px; margin: 0 auto; }

        /* Logo & Header */
        .header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #007ead; letter-spacing: 1px; }

        /* Section Title */
        .section-header { font-size: 12px; font-weight: 800; color: #888; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; border-left: 4px solid #007ead; padding-left: 10px; }

        /* Metric Grids */
        .metric-grid { display: grid; gap: 15px; margin-bottom: 30px; }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }

        .metric-card { background: white; border-radius: 10px; padding: 15px; box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.03); }
        .m-val { font-size: 24px; font-weight: bold; color: var(--blue-main); }
        .m-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #666; margin: 5px 0 10px; }
        .m-chart { height: 60px; }
        .m-footer { display: flex; justify-content: space-between; font-size: 10px; color: #888; border-top: 1px solid #f0f0f0; padding-top: 8px; margin-top: 8px; }
        .m-avg { background: #e9f2f9; color: #005a9c; padding: 2px 5px; border-radius: 3px; font-weight: 600; }

        /* Main Charts Section */
        .card-large { background: white; border-radius: 12px; padding: 25px; box-shadow: var(--card-shadow); margin-bottom: 25px; }
        h2 { font-size: 1.1rem; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; color: #444; }

        /* Waterfall Layout */
        .wf-layout { display: flex; gap: 30px; }
        .wf-table { width: 35%; border-right: 1px solid #f0f0f0; padding-right: 20px; }
        .wf-chart { width: 65%; }
        
        .row { display: flex; justify-content: space-between; padding: 10px; border-radius: 5px; font-size: 13px; cursor: pointer; position: relative; }
        .row:hover { background: #f8f9fa; }
        .row .tooltip { visibility: hidden; width: 220px; background-color: white; border: 1px solid #ddd; padding: 10px; border-radius: 5px; position: absolute; z-index: 10; left: 20px; top: 35px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); color: #555; font-size: 11px; line-height: 1.4; }
        .row:hover .tooltip { visibility: visible; }
        .val-p { font-weight: bold; }
        .val-n { font-weight: bold; color: #e57373; }
    </style>

<div class="d-flex flex-column flex-lg-row">
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card border border-gray-300 rounded-1">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                <div class="d-flex justify-content-between flex-column border-bottom border-gray-300 p-4 px-6">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column flex-grow-1">
                            <h2 class="pt-2 text-dark">
                                <span class="las la-file-alt text-dark me-4"></span>
                                Daftar Jurnal
                            </h2>
                        </div>
                    </div>
                </div>
<div class="container">
    <div class="header">
        <div class="logo-text">JASA KARTINI <span style="font-weight: 300;">GROUP</span></div>
    </div>

    <div class="section-header">Core Financials (Billion IDR)</div>
    <div class="metric-grid grid-3" id="core-container"></div>

    <div class="section-header">Margin Analysis (%)</div>
    <div class="metric-grid grid-4" id="margin-container"></div>

    <div class="card-large">
        <h2>📊 Financial Flow (Sankey)</h2>
        <div id="sankey-chart" style="height: 350px;"></div>
    </div>

    <div class="card-large">
        <h2>📉 Earnings Waterfall Analysis</h2>
        <div class="wf-layout">
            <div class="wf-table">
                <div class="row"><span>Revenue</span><span class="val-p">1,552.6B</span></div>
                <div class="row" style="padding-left:25px; color:#777"><span>Cost of Revenue</span><span class="val-n">-852.4B</span></div>
                <div class="row"><span>Gross Profit</span><span class="val-p">700.2B</span></div>
                <div class="row" style="padding-left:25px; color:#777"><span>Operating Expenses</span><span class="val-n">-616.2B</span></div>
                <div class="row"><span>Operating Income</span><span class="val-p">84.0B</span></div>
                <div class="row">
                    <span>Other Expenses</span><span class="val-n">-71.6B</span>
                    <div class="tooltip">Beban non-operasional seperti pembayaran bunga, pajak, serta biaya tidak rutin lainnya.</div>
                </div>
                <div class="row" style="background:#f0f4f8; font-weight:bold; margin-top:10px;"><span>Net Income</span><span class="val-p">12.4B</span></div>
            </div>
            <div class="wf-chart" id="wf-apex"></div>
        </div>
    </div>
</div>
</div>
<script>
    // 1. Core Financials Data
    const coreMetrics = [
        {t: 'Revenue', v: '1,552.6B', d: [1100, 1150, 1200, 1350, 1400, 1450, 1552.6], avg: '-4%'},
        {t: 'Operating Income', v: '84.0B', d: [200, 180, 150, 120, 100, 90, 84], avg: '-59%'},
        {t: 'Net Income', v: '12.4B', d: [150, 120, 100, 80, 50, 20, 12.4], avg: '-87%'}
    ];

    // 2. Margin Metrics Data
    const marginMetrics = [
        {t: 'Gross Margin', v: '45.1%', d: [40,42,45,44,46,45,47,46,45,45,45.1], avg: '46%'},
        {t: 'Operating Margin', v: '5.4%', d: [15,14,12,10,8,7,6,5.5,5,5.2,5.4], avg: '7%'},
        {t: 'Net Margin', v: '0.8%', d: [8,7,6,5,4,3,2,1,0.5,0.7,0.8], avg: '1%'},
        {t: 'FCF Margin', v: '-5.6%', d: [2,-1,-3,-5,-8,-7,-6,-5,-4,-5,-5.6], avg: '-8%', neg:true}
    ];

    // Helper function to render cards
    function renderCards(targetId, dataArray, prefix) {
        const target = document.getElementById(targetId);
        dataArray.forEach((m, i) => {
            target.innerHTML += `
                <div class="metric-card">
                    <div class="m-val" style="color: ${m.neg ? '#e57373' : '#1d70b8'}">${m.v}</div>
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

    // Helper function to render charts
    function renderCharts(dataArray, prefix) {
        dataArray.forEach((m, i) => {
            new Chart(document.getElementById(`${prefix}Chart${i}`).getContext('2d'), {
                type: 'bar',
                data: { 
                    labels: m.d.map(x=>''), 
                    datasets: [{ 
                        data: m.d, 
                        backgroundColor: m.neg ? '#e57373' : (prefix === 'core' ? '#1d70b8' : '#006d77'), 
                        borderRadius: 2, 
                        barThickness: 6 
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

    // Initialize View
    renderCards('core-container', coreMetrics, 'core');
    renderCards('margin-container', marginMetrics, 'margin');
    renderCharts(coreMetrics, 'core');
    renderCharts(marginMetrics, 'margin');

    // Sankey & Waterfall Logic (Tetap sama)
    const drawSankey = () => {
        const width = 1000, height = 350;
        const svg = d3.select("#sankey-chart").append("svg").attr("viewBox", `0 0 ${width} ${height}`).append("g");
        const sankey = d3.sankey().nodeWidth(20).nodePadding(40).extent([[5, 5], [width - 5, height - 5]]);
        const data = {
            nodes: [{name: "Revenue"}, {name: "Gross Profit"}, {name: "Cost of Revenue"}, {name: "Net Income"}, {name: "Expenses"}, {name: "SG&A"}, {name: "Other"}],
            links: [
                {source: 0, target: 1, value: 700.3}, {source: 0, target: 2, value: 852.7},
                {source: 1, target: 3, value: 12.4}, {source: 1, target: 4, value: 687.9},
                {source: 4, target: 5, value: 499.4}, {source: 4, target: 6, value: 188.5}
            ]
        };
        const {nodes, links} = sankey(data);
        svg.append("g").selectAll("path").data(links).enter().append("path").attr("d", d3.sankeyLinkHorizontal()).attr("fill", "none")
            .attr("stroke", d => d.target.name.includes("Income") || d.target.name.includes("Profit") ? "#aab8c2" : "#f8d7da").attr("stroke-opacity", 0.4).attr("stroke-width", d => Math.max(1, d.width));
        const node = svg.append("g").selectAll("g").data(nodes).enter().append("g");
        node.append("rect").attr("x", d => d.x0).attr("y", d => d.y0).attr("height", d => d.y1 - d.y0).attr("width", 20).attr("fill", d => d.name === "Revenue" ? "#4a76a8" : "#cfd8dc").attr("rx", 4);
        node.append("text").attr("x", d => d.x0 - 10).attr("y", d => (d.y1 + d.y0) / 2).attr("dy", "0.35em").attr("text-anchor", "end").style("font-size", "12px").text(d => d.name).filter(d => d.x0 < 500).attr("x", d => d.x1 + 10).attr("text-anchor", "start");
    };

    new ApexCharts(document.querySelector("#wf-apex"), {
        series: [{ data: [
            { x: 'Rev', y: [0, 1553], color: '#aab8c2' },
            { x: 'CoR', y: [700, 1553], color: '#e58a8a' },
            { x: 'GP', y: [0, 700], color: '#aab8c2' },
            { x: 'OpEx', y: [84, 700], color: '#e58a8a' },
            { x: 'OpInc', y: [0, 84], color: '#aab8c2' },
            { x: 'Other', y: [12, 84], color: '#e58a8a' },
            { x: 'Net', y: [0, 12], color: '#607d8b' }
        ]}],
        chart: { type: 'rangeBar', height: 350, toolbar: {show:false} },
        plotOptions: { bar: { columnWidth: '50%' } },
        dataLabels: { enabled: true, formatter: (val, opt) => (opt.dataPointIndex % 2 !== 0 ? '-' : '') + Math.abs(val[1]-val[0]) + 'B' },
        yaxis: { show: false },
        grid: { strokeDashArray: 4 }
    }).render();

    drawSankey();
</script>

