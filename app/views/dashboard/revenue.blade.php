@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #1a237e;
            --secondary: #3949ab;
            --bg: #f0f2f5;
            --text: #333;
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 20px; color: var(--text); }
        .container { max-width: 1200px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        /* Header */
        .header { text-align: center; border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 26px; color: var(--primary); letter-spacing: 1px; }

        /* Stats Row */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; text-align: center; }
        .card.dark { background: var(--primary); color: white; }
        .card h3 { font-size: 13px; margin: 0 0 10px 0; opacity: 0.9; }
        .card .value { font-size: 22px; font-weight: bold; }
        .card .sub { font-size: 12px; color: #4caf50; font-weight: bold; margin-top: 5px; }

        /* Charts Row */
        .charts-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 25px; }
        .chart-container { border: 1px solid #eee; border-radius: 8px; padding: 15px; background: #fff; }
        .chart-title { font-size: 14px; font-weight: bold; margin-bottom: 15px; text-align: center; }

        /* Table & Target Row */
        .bottom-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .growth { color: #2e7d32; font-weight: bold; }

        .target-item { display: flex; align-items: center; margin-bottom: 12px; font-size: 12px; }
        .bar-bg { flex-grow: 1; background: #eee; height: 10px; margin: 0 10px; border-radius: 5px; }
        .bar-fill { height: 100%; border-radius: 5px; }
    </style>

<div class="d-flex flex-column flex-lg-row">
    <div class="flex-lg-row-fluid">
        <!--begin::Contents-->
        <div class="card border border-gray-300 rounded-1">
            <!--begin::Card header-->
            <div class="card-body p-0" id="kt_content_header">
                            <h2 class="pt-2 text-dark">
                            &nbsp;</h2>
                        </div>
                    </div>
                </div>
</div>
<div class="container">
    <div class="header">
        <h1>HOSPITAL REVENUE CONSOLE</h1>
        <p>Branch Performance Overview - FY2026 (YTD)</p>
    </div>

    <div class="stats-grid">
        <div class="card dark">
            <h3>Total Revenue YTD</h3>
            <div class="value">Rp 182,30 M</div>
        </div>
        <div class="card">
            <h3>Growth YoY</h3>
            <div class="value" style="color:#2e7d32">+9.5% ↑</div>
            <div class="sub">▲ +7.0% YoY</div>
        </div>
        <div class="card">
            <h3>% of Target</h3>
            <div class="bar-bg" style="margin-top:15px;"><div class="bar-fill" style="width:94%; background:#2e7d32;"></div></div>
        </div>
        <div class="card">
            <h3>Total Patients YTD</h3>
            <div class="value" style="color:#f57c00">520,345</div>
            <div style="margin-top:5px; letter-spacing: 2px;">👤👤👤👤👤</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-container">
            <div class="chart-title">Monthly Revenue Trend</div>
            <canvas id="revenueTrendChart"></canvas>
        </div>
        <div class="chart-container">
            <div class="chart-title">Revenue Share by Branch</div>
            <canvas id="revenueShareChart"></canvas>
        </div>
    </div>

    <div class="bottom-grid">
        <div class="chart-container">
            <div class="chart-title" style="text-align:left">Branch Performance</div>
            <table>
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Revenue YTD</th>
                        <th>Target FY</th>
                        <th>% vs Target</th>
                        <th>Growth YoY</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>RS Jasa Kartini</td><td>Rp 42,85 M</td><td>Rp 45,00 M</td><td style="color:green;font-weight:bold">95.2%</td><td class="growth">▲ 8.1%</td></tr>
                    <tr><td>Klinik JK Tasikmalaya</td><td>Rp 28,75 M</td><td>Rp 30,00 M</td><td style="color:green;font-weight:bold">95.8%</td><td class="growth">▲ 10.4%</td></tr>
                    <tr><td>Klinik JK Ciamis</td><td>Rp 23,15 M</td><td>Rp 25,00 M</td><td style="color:green;font-weight:bold">92.6%</td><td class="growth">▲ 7.9%</td></tr>
                    <tr><td>Lab JK Bandung</td><td>Rp 25,94 M</td><td>Rp 28,00 M</td><td style="color:green;font-weight:bold">92.6%</td><td class="growth">▲ 11.3%</td></tr>
                </tbody>
            </table>
        </div>

        <div class="chart-container">
            <div class="chart-title">Revenue vs Target</div>
            <div class="target-item"><span>RS Jasa Kartini</span><div class="bar-bg"><div class="bar-fill" style="width:95%; background:#1a237e"></div></div><span>95%</span></div>
            <div class="target-item"><span>Klinik JK Tasik</span><div class="bar-bg"><div class="bar-fill" style="width:96%; background:#f57c00"></div></div><span>96%</span></div>
            <div class="target-item"><span>Klinik JK Ciamis</span><div class="bar-bg"><div class="bar-fill" style="width:93%; background:#388e3c"></div></div><span>93%</span></div>
            <div class="target-item"><span>Klinik JK Banjar</span><div class="bar-bg"><div class="bar-fill" style="width:94%; background:#673ab7"></div></div><span>94%</span></div>
        </div>
    </div>
</div>
</div></div></div></div>
<script>
    // 1. Grafik Area (Trend Bulanan)
    const ctxTrend = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'RS Jasa Kartini',
                    data: [21, 19, 20, 19, 19, 20, 18, 18.5, 19, 18, 18.5, 19],
                    borderColor: '#1a237e',
                    backgroundColor: 'rgba(26, 35, 126, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Klinik JK Tasik',
                    data: [11, 10.5, 11.5, 12, 11, 9, 10.5, 9.5, 10, 9.5, 10.2, 10.5],
                    borderColor: '#f57c00',
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } }
    });

    // 2. Grafik Pie (Revenue Share)
    const ctxShare = document.getElementById('revenueShareChart').getContext('2d');
    new Chart(ctxShare, {
        type: 'pie',
        data: {
            labels: ['RS Jasa Kartini', 'Klinik JK Tasik', 'Klinik JK Ciamis', 'Klinik JK Banjar', 'Lainnya'],
            datasets: [{
                data: [23.5, 12.7, 14.3, 10.6, 38.9],
                backgroundColor: ['#1a237e', '#f57c00', '#388e3c', '#673ab7', '#009688']
            }]
        },
        options: { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } } } }
    });
</script>


