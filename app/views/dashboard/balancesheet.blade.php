@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --blue-main: #1d70b8;
            --red-main: #ef4444;
            --bg-body: #f4f7f9;
            --card-shadow: 0 2px 12px rgba(0,0,0,0.06);
            --text-muted: #666;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-body); padding: 30px; }
        
        .card-large { 
            background: white; border-radius: 12px; padding: 25px; 
            box-shadow: var(--card-shadow); max-width: 1000px; margin: 0 auto;
        }

        /* Tabs */
        .tabs { display: flex; border-bottom: 1px solid #eee; margin-bottom: 20px; }
        .tab { 
            padding: 10px 25px; cursor: pointer; font-weight: bold; 
            position: relative; transition: 0.3s; opacity: 0.5;
        }
        .tab.active { opacity: 1; color: var(--blue-main); }
        .tab.active.liab-tab { color: var(--red-main); }
        .tab.active::after { 
            content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; 
            background: var(--blue-main); 
        }
        .tab.active.liab-tab::after { background: var(--red-main); }
        
        .tab-val { display: block; font-size: 20px; }
        .tab-label { font-size: 11px; text-transform: uppercase; color: var(--text-muted); display:flex; align-items:center; gap:5px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* Layout */
        .bs-layout { display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; min-height: 350px; }
        .bs-row { display: flex; justify-content: space-between; padding: 8px 10px; border-bottom: 1px solid #f9f9f9; font-size: 13px; border-radius: 4px; }
        .bs-row.parent { font-weight: bold; margin-top: 10px; background: #fafafa; }
        .bs-row.child { padding-left: 25px; color: var(--text-muted); cursor: pointer; }
        .bs-row.child:hover { background: #f0f7ff; color: var(--blue-main); }

        /* Chart */
        .chart-container { position: relative; height: 320px; }
        .chart-center-text {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%); text-align: center; pointer-events: none;
        }
        .center-val { font-size: 18px; font-weight: bold; display: block; }
        .center-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; }
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

<div class="card-large">

    <div class="tabs">
        <div id="tab-assets" class="tab active" onclick="switchTab('assets')">
            <span class="tab-val">3,635.6B</span>
            <span class="tab-label"><span class="dot" style="background:var(--blue-main)"></span> Assets</span>
        </div>
        <div id="tab-liabilities" class="tab liab-tab" onclick="switchTab('liabilities')">
            <span class="tab-val">1,917.3B</span>
            <span class="tab-label"><span class="dot" style="background:var(--red-main)"></span> Liabilities</span>
        </div>
    </div>

    <div class="bs-layout">
        <div id="list-container" class="bs-list">
            </div>

        <div class="chart-container">
            <canvas id="donutChart"></canvas>
            <div class="chart-center-text">
                <span id="center-label" class="center-label">Total Assets</span>
                <span id="center-val" class="center-val">3,636B <small>IDR</small></span>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    const dataModel = {
        assets: {
            total: "3,636B",
            label: "Total Assets",
            color: '#1d70b8',
            items: [
                { name: "Current Assets", val: "1,160.5B", isParent: true },
                { name: "Cash & Short-Term Investments", val: "817.2B", chartIdx: 1 },
                { name: "Receivables", val: "213.4B", chartIdx: 2 },
                { name: "Other Current Assets", val: "129.9B", chartIdx: 3 },
                { name: "Non-Current Assets", val: "2,475.1B", isParent: true },
                { name: "PP&E", val: "2,188.0B", chartIdx: 0 },
                { name: "Intangibles", val: "94.8B", chartIdx: 4 },
                { name: "Long-Term Investments", val: "88.9B", chartIdx: 6 }
            ],
            chart: {
                labels: ['PP&E', 'Cash & Inv', 'Receivables', 'Other Curr', 'Intangibles', 'Other Non-Curr', 'LT-Inv'],
                values: [60.18, 22.48, 5.87, 3.57, 2.61, 2.85, 2.44],
                colors: ['#1d70b8', '#4a90e2', '#7fb3d5', '#a9cce3', '#d4e6f1', '#ebf5fb', '#f4f7f9']
            }
        },
        liabilities: {
            total: "1,917.3B",
            label: "Total Liabilities",
            color: '#ef4444',
            items: [
                { name: "Current Liabilities", val: "593.4B", isParent: true },
                { name: "Short-Term Debt", val: "442.3B", chartIdx: 2 },
                { name: "Accounts Payable", val: "42.6B", chartIdx: 0 },
                { name: "Other Current Liabilities", val: "78.8B", chartIdx: 3 },
                { name: "Non-Current Liabilities", val: "1,323.9B", isParent: true },
                { name: "Long-Term Debt", val: "851.5B", chartIdx: 5 },
                { name: "Other Non-Current Liabilities", val: "472.4B", chartIdx: 4 }
            ],
            chart: {
                labels: ['Accounts Payable', 'Accrued Liab', 'Short-Term Debt', 'Other Curr', 'Other Non-Curr', 'Long-Term Debt'],
                values: [2.22, 1.55, 23.07, 4.11, 24.64, 44.41],
                colors: ['#455a64', '#607d8b', '#90a4ae', '#cfd8dc', '#b0bec5', '#78909c']
            }
        }
    };

    let currentChart;
    const ctx = document.getElementById('donutChart').getContext('2d');

    function switchTab(type) {
        // Update UI Tabs
        document.getElementById('tab-assets').classList.toggle('active', type === 'assets');
        document.getElementById('tab-liabilities').classList.toggle('active', type === 'liabilities');
        
        const data = dataModel[type];
        
        // Update Center Text
        document.getElementById('center-label').innerText = data.label;
        document.getElementById('center-val').innerHTML = `${data.total} <small>IDR</small>`;

        // Update List
        const listContainer = document.getElementById('list-container');
        listContainer.innerHTML = '';
        data.items.forEach(item => {
            const row = document.createElement('div');
            row.className = `bs-row ${item.isParent ? 'parent' : 'child'}`;
            if(!item.isParent) {
                row.onmouseover = () => triggerHighlight(item.chartIdx);
                row.onmouseout = () => triggerHighlight(-1);
            }
            row.innerHTML = `<span>${item.name}</span><span>${item.val}</span>`;
            listContainer.appendChild(row);
        });

        // Update Chart
        if (currentChart) currentChart.destroy();
        currentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.chart.labels,
                datasets: [{
                    data: data.chart.values,
                    backgroundColor: data.chart.colors,
                    borderWidth: 2, hoverOffset: 15
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } },
                maintainAspectRatio: false
            }
        });
    }

    function triggerHighlight(idx) {
        if (!currentChart) return;
        currentChart.setActiveElements(idx === -1 ? [] : [{ datasetIndex: 0, index: idx }]);
        currentChart.tooltip.setActiveElements(idx === -1 ? [] : [{ datasetIndex: 0, index: idx }], {x:0, y:0});
        currentChart.update();
    }

    // Inisialisasi awal
    switchTab('assets');
</script>


