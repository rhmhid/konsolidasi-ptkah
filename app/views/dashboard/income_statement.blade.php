@extends('layouts.main')
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0B1A2E;--navy2:#142240;--teal:#0FA896;--teal2:#0D8E7D;
  --gold:#E8A820;--gold2:#C48A10;--red:#E24B4A;--green:#3B9B5A;
  --bg:#F4F7FA;--card:#fff;--border:#D8DFE8;
  --txt:#1A2A3A;--txt2:#4A6070;--txt3:#8899AA;
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--txt);font-size:13px}
@media(prefers-color-scheme:dark){
  :root{--bg:#0B1018;--card:#141E2D;--border:#1E2D40;--txt:#D0DCE8;--txt2:#8899AA;--txt3:#4A5D70}
}

#app{display:flex;height:100vh;min-height:600px;overflow:hidden}

/* SIDEBAR */
#sidebar{width:220px;min-width:220px;background:var(--navy);color:#fff;display:flex;flex-direction:column;padding:0}
.logo{padding:20px 18px 14px;border-bottom:1px solid rgba(255,255,255,.1)}
.logo-title{font-size:15px;font-weight:700;color:#fff;letter-spacing:.3px}
.logo-sub{font-size:10px;color:rgba(255,255,255,.45);margin-top:3px;text-transform:uppercase;letter-spacing:1px}
.nav-section{padding:10px 0 6px;color:rgba(255,255,255,.35);font-size:9px;text-transform:uppercase;letter-spacing:1.2px;padding-left:18px;margin-top:8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 18px;cursor:pointer;border-left:3px solid transparent;transition:all .15s;color:rgba(255,255,255,.65);font-size:12.5px}
.nav-item:hover{background:rgba(255,255,255,.07);color:#fff}
.nav-item.active{background:rgba(15,168,150,.18);border-left-color:var(--teal);color:#fff}
.nav-icon{width:16px;height:16px;opacity:.8;font-size:14px}
.nav-divider{height:1px;background:rgba(255,255,255,.08);margin:8px 12px}
.branch-filter{padding:12px 14px;margin:6px 10px;background:rgba(255,255,255,.07);border-radius:8px}
.branch-filter label{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px}
.branch-filter select{width:100%;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;padding:5px 8px;border-radius:5px;font-size:11px;cursor:pointer}
.branch-filter select option{background:#142240;color:#fff}
.period-filter{padding:8px 14px;margin:0 10px 6px;background:rgba(255,255,255,.05);border-radius:8px}
.period-filter label{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px}
.period-filter select{width:100%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#fff;padding:5px 8px;border-radius:5px;font-size:11px;cursor:pointer}
.period-filter select option{background:#142240}

/* MAIN */
#main{flex:1;overflow-y:auto;background:var(--bg)}
.topbar{background:var(--card);border-bottom:1px solid var(--border);padding:12px 24px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:10}
.page-title{font-size:16px;font-weight:600;color:var(--txt)}
.page-subtitle{font-size:11px;color:var(--txt3);margin-top:2px}
.topbar-right{display:flex;gap:10px;align-items:center}
.badge-live{background:#E8F9F5;color:#0FA896;font-size:10px;padding:4px 10px;border-radius:20px;font-weight:600;border:1px solid rgba(15,168,150,.25)}
@media(prefers-color-scheme:dark){.badge-live{background:rgba(15,168,150,.15)}}
.export-btn{background:var(--navy);color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:11px;cursor:pointer;font-weight:500}
.export-btn:hover{background:var(--navy2)}

.content{padding:20px 24px}

/* KPI ROW */
.kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.kpi-card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px 16px;position:relative;overflow:hidden}
.kpi-card::before{content:'';position:absolute;top:0;left:0;width:3px;height:100%;background:var(--accent-color,var(--teal))}
.kpi-label{font-size:10px;color:var(--txt3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
.kpi-value{font-size:22px;font-weight:700;color:var(--txt);line-height:1}
.kpi-sub{font-size:10px;margin-top:6px;display:flex;align-items:center;gap:4px}
.kpi-up{color:var(--green)}
.kpi-down{color:var(--red)}
.kpi-neutral{color:var(--txt3)}

/* SECTION */
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.section-title{font-size:13px;font-weight:600;color:var(--txt)}
.section-tag{font-size:10px;color:var(--txt3);background:var(--bg);border:1px solid var(--border);padding:3px 10px;border-radius:12px}
.card{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:16px}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px}
.full-card{margin-bottom:20px}

/* TABLE */
.fin-table{width:100%;border-collapse:collapse;font-size:11.5px}
.fin-table thead th{padding:7px 10px;text-align:left;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--txt3);border-bottom:1.5px solid var(--border);background:var(--bg)}
.fin-table thead th:not(:first-child){text-align:right}
.fin-table tbody tr:hover{background:rgba(15,168,150,.04)}
.fin-table td{padding:7px 10px;border-bottom:1px solid var(--border);color:var(--txt2)}
.fin-table td:not(:first-child){text-align:right;font-variant-numeric:tabular-nums;font-family:monospace}
.fin-table .row-header{font-weight:600;color:var(--txt);font-size:12px}
.fin-table .row-total{font-weight:700;color:var(--txt);background:var(--bg);font-size:12px}
.fin-table .row-subtotal{font-weight:600;color:var(--teal2);font-style:italic}
.fin-table .indent{padding-left:24px}
.fin-table .positive{color:var(--green)}
.fin-table .negative{color:var(--red)}
.fin-table .section-group{background:rgba(11,26,46,.04);color:var(--txt);font-weight:700;font-size:11px}
@media(prefers-color-scheme:dark){.fin-table .section-group{background:rgba(255,255,255,.04)}}

/* CHART wrappers */
.chart-wrap{position:relative;width:100%}
.chart-label{font-size:10px;color:var(--txt3);margin-bottom:8px;font-weight:500;text-transform:uppercase;letter-spacing:.6px}
.legend-row{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:10px;font-size:11px;color:var(--txt2)}
.legend-dot{width:10px;height:10px;border-radius:2px;display:inline-block;margin-right:5px;vertical-align:middle}

/* BRANCH PERF */
.branch-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)}
.branch-row:last-child{border-bottom:none}
.branch-name{width:130px;font-size:11.5px;font-weight:500;color:var(--txt)}
.branch-bar-wrap{flex:1;height:8px;background:var(--bg);border-radius:4px;overflow:hidden}
.branch-bar{height:100%;border-radius:4px;background:var(--teal);transition:width .4s}
.branch-val{width:80px;text-align:right;font-size:11px;color:var(--txt2);font-variant-numeric:tabular-nums}
.branch-pct{width:42px;text-align:right;font-size:10px;font-weight:600}

/* TABS */
.tab-row{display:flex;gap:2px;background:var(--bg);border-radius:8px;padding:3px;margin-bottom:14px;border:1px solid var(--border)}
.tab-btn{flex:1;padding:7px;border:none;background:transparent;cursor:pointer;font-size:11.5px;color:var(--txt2);border-radius:6px;font-weight:500;transition:all .15s}
.tab-btn.active{background:var(--card);color:var(--txt);box-shadow:0 1px 4px rgba(0,0,0,.08)}


    
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
                                {!! get_combo_option_month_lk( date('m')) !!}
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

  <div id="page-laba-rugi" class="page">
    <div class="content">
      <div class="kpi-row">
        <div class="kpi-card" style="--accent-color:#0FA896">
          <div class="kpi-label">Total Pendapatan</div>
          <div class="kpi-value">Rp 487,3M</div>
          <div class="kpi-sub kpi-up">▲ 12,4% vs tahun lalu</div>
        </div>
        <div class="kpi-card" style="--accent-color:#3B9B5A">
          <div class="kpi-label">Laba Bersih</div>
          <div class="kpi-value">Rp 68,5M</div>
          <div class="kpi-sub kpi-up">▲ 8,7% vs tahun lalu</div>
        </div>
        <div class="kpi-card" style="--accent-color:#E8A820">
          <div class="kpi-label">EBITDA</div>
          <div class="kpi-value">Rp 112,1M</div>
          <div class="kpi-sub kpi-up">▲ 9,2% vs tahun lalu</div>
        </div>
        <div class="kpi-card" style="--accent-color:#E24B4A">
          <div class="kpi-label">Net Profit Margin</div>
          <div class="kpi-value">14,1%</div>
          <div class="kpi-sub kpi-down">▼ 0,5pp vs tahun lalu</div>
        </div>
      </div>

      <div class="full-card card">
        <div class="section-header">
          <div class="section-title">Laporan Laba Rugi Konsolidasi</div>
          <div class="tab-row" style="width:280px;margin-bottom:0">
            <button class="tab-btn" onclick="switchLRTab(&#39;detail&#39;,this)">Detail</button>
            <button class="tab-btn active" onclick="switchLRTab(&#39;perbandingan&#39;,this)">YoY</button>
            <button class="tab-btn" onclick="switchLRTab(&#39;cabang&#39;,this)">Per Cabang</button>
          </div>
        </div>

        <div id="lr-detail" style="display: none;">
          <table class="fin-table">
            <thead><tr><th>Keterangan</th><th>FY 2024 (Rp Juta)</th><th>FY 2023 (Rp Juta)</th><th>Selisih</th></tr></thead>
            <tbody>
              <tr><td colspan="4" class="section-group" style="padding:7px 10px">PENDAPATAN OPERASIONAL</td></tr>
              <tr><td class="indent">Pendapatan Rawat Inap</td><td>198.450</td><td>172.300</td><td class="positive">+15,2%</td></tr>
              <tr><td class="indent">Pendapatan Rawat Jalan</td><td>143.280</td><td>131.500</td><td class="positive">+9,0%</td></tr>
              <tr><td class="indent">Pendapatan IGD</td><td>67.940</td><td>61.200</td><td class="positive">+11,0%</td></tr>
              <tr><td class="indent">Pendapatan Penunjang Medis</td><td>52.300</td><td>46.800</td><td class="positive">+11,8%</td></tr>
              <tr><td class="indent">Pendapatan Farmasi</td><td>25.330</td><td>22.700</td><td class="positive">+11,6%</td></tr>
              <tr class="row-subtotal"><td class="row-header">Total Pendapatan</td><td>487.300</td><td>434.500</td><td class="positive">+12,4%</td></tr>
              <tr><td colspan="4" class="section-group" style="padding:7px 10px">BEBAN OPERASIONAL</td></tr>
              <tr><td class="indent">Beban Gaji &amp; Tunjangan</td><td>156.800</td><td>138.400</td><td class="negative">+13,3%</td></tr>
              <tr><td class="indent">Beban Obat &amp; BHP</td><td>97.450</td><td>87.200</td><td class="negative">+11,8%</td></tr>
              <tr><td class="indent">Beban Umum &amp; Administrasi</td><td>42.600</td><td>39.100</td><td class="negative">+9,0%</td></tr>
              <tr><td class="indent">Beban Pemeliharaan</td><td>21.300</td><td>19.800</td><td class="negative">+7,6%</td></tr>
              <tr><td class="indent">Depresiasi &amp; Amortisasi</td><td>18.650</td><td>17.200</td><td class="negative">+8,4%</td></tr>
              <tr class="row-subtotal"><td class="row-header">Total Beban</td><td>336.800</td><td>301.700</td><td class="negative">+11,6%</td></tr>
              <tr><td colspan="4" class="section-group" style="padding:7px 10px">PROFITABILITAS</td></tr>
              <tr><td class="row-header">Laba Operasional (EBIT)</td><td>93.850</td><td>84.600</td><td class="positive">+10,9%</td></tr>
              <tr><td class="indent">Pendapatan Non-Operasional</td><td>4.200</td><td>3.800</td><td class="positive">+10,5%</td></tr>
              <tr><td class="indent">Beban Bunga &amp; Keuangan</td><td>(12.400)</td><td>(11.800)</td><td class="negative">+5,1%</td></tr>
              <tr><td class="indent">Pajak Penghasilan</td><td>(17.150)</td><td>(14.420)</td><td class="negative">+19,0%</td></tr>
              <tr class="row-total"><td>LABA BERSIH</td><td>68.500</td><td>62.180</td><td class="positive">+10,2%</td></tr>
            </tbody>
          </table>
        </div>

        <div id="lr-perbandingan" style="display: block;">
          <div class="legend-row">
            <span><span class="legend-dot" style="background:#0FA896"></span>FY 2024</span>
            <span><span class="legend-dot" style="background:#8899AA"></span>FY 2023</span>
          </div>
          <div class="chart-wrap" style="height:280px">
            <canvas id="lrYoYChart" role="img" aria-label="Perbandingan komponen laba rugi FY 2024 vs FY 2023" height="251" style="display: block; box-sizing: border-box; height: 280px; width: 1831px;" width="1647">Perbandingan pendapatan, beban, dan laba bersih tahun 2024 vs 2023.</canvas>
          </div>
        </div>

        <div id="lr-cabang" style="display: none;">
          <div class="chart-wrap" style="height:280px">
            <canvas id="lrCabangChart" role="img" aria-label="Laba bersih per cabang rumah sakit" height="251" style="display: block; box-sizing: border-box; height: 280px; width: 1831px;" width="1647">Laba bersih per cabang RS Group.</canvas>
          </div>
        </div>
      </div>

      <div class="two-col">
        <div class="card">
          <div class="section-header"><div class="section-title">Komposisi Pendapatan</div></div>
          <div class="legend-row">
            <span><span class="legend-dot" style="background:#0FA896"></span>Rawat Inap</span>
            <span><span class="legend-dot" style="background:#E8A820"></span>Rawat Jalan</span>
            <span><span class="legend-dot" style="background:#0B1A2E"></span>IGD</span>
            <span><span class="legend-dot" style="background:#3B9B5A"></span>Penunjang</span>
            <span><span class="legend-dot" style="background:#E24B4A"></span>Farmasi</span>
          </div>
          <div class="chart-wrap" style="height:200px">
            <canvas id="revPieChart" role="img" aria-label="Komposisi pendapatan per segmen" width="802" height="179" style="display: block; box-sizing: border-box; height: 200px; width: 891px;">Rawat Inap 40.7%, Rawat Jalan 29.4%, IGD 13.9%, Penunjang 10.7%, Farmasi 5.2%.</canvas>
          </div>
        </div>
        <div class="card">
          <div class="section-header"><div class="section-title">Tren Margin Bulanan</div></div>
          <div class="legend-row">
            <span><span class="legend-dot" style="background:#0FA896"></span>Gross Margin</span>
            <span><span class="legend-dot" style="background:#E8A820"></span>Net Margin</span>
          </div>
          <div class="chart-wrap" style="height:200px">
            <canvas id="marginTrendChart" role="img" aria-label="Tren gross margin dan net margin sepanjang 2024" width="802" height="179" style="display: block; box-sizing: border-box; height: 200px; width: 891px;">Tren margin bulanan FY 2024.</canvas>
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

<script src="{{ asset('assets/js/chart.umd.js.download') }}"></script>
<script>
const pageTitles={
  'laba-rugi':['Laporan Laba &amp; Rugi','Konsolidasi · FY 2024 · 7 Rumah Sakit'],
  'neraca':['Neraca Konsolidasi','Posisi Keuangan · 31 Desember 2024'],
  'revenue':['Revenue Rumah Sakit','Konsolidasi &amp; Per Cabang · FY 2024'],
  'cashflow':['Laporan Arus Kas','Cash Flow Statement · FY 2024']
};

function showPage(id,el){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('page-'+id).classList.add('active');
  el.classList.add('active');
  const t=pageTitles[id];
  document.getElementById('pageTitle').innerHTML=t[0];
  document.getElementById('pageSubtitle').innerHTML=t[1];
}

function filterBranch(v){console.log('Branch:',v)}
function filterPeriod(v){console.log('Period:',v)}

function switchLRTab(tab,el){
  document.querySelectorAll('#page-laba-rugi .tab-btn').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('lr-detail').style.display='none';
  document.getElementById('lr-perbandingan').style.display='none';
  document.getElementById('lr-cabang').style.display='none';
  document.getElementById('lr-'+tab).style.display='block';
}

const MONTHS=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

function isDark(){return matchMedia('(prefers-color-scheme:dark)').matches}
const gridColor=()=>isDark()?'rgba(255,255,255,.08)':'rgba(0,0,0,.06)';
const txtColor=()=>isDark()?'#8899AA':'#6A7A88';

const defs={
  responsive:true,maintainAspectRatio:false,
  plugins:{legend:{display:false}},
  scales:{
    x:{grid:{color:gridColor()},ticks:{color:txtColor(),font:{size:10}}},
    y:{grid:{color:gridColor()},ticks:{color:txtColor(),font:{size:10}}}
  }
};

new Chart(document.getElementById('lrYoYChart'),{
  type:'bar',
  data:{
    labels:['Total Pendapatan','Total Beban','Laba Kotor','EBITDA','Laba Bersih'],
    datasets:[
      {label:'FY 2024',data:[487.3,336.8,150.5,112.1,68.5],backgroundColor:'#0FA896',borderRadius:4,borderSkipped:false},
      {label:'FY 2023',data:[434.5,301.7,132.8,102.7,62.2],backgroundColor:'rgba(136,153,170,.45)',borderRadius:4,borderSkipped:false}
    ]
  },
  options:{...defs,plugins:{legend:{display:false}}}
});

new Chart(document.getElementById('lrCabangChart'),{
  type:'bar',
  data:{
    labels:['Harapan','Mitra','Prima','Sejahtera','Sentosa','Anugrah','Kartika'],
    datasets:[{
      label:'Laba Bersih (Rp Juta)',
      data:[16.8,14.2,11.8,9.4,7.6,5.2,3.5],
      backgroundColor:['#0FA896','#0FA896','#0FA896','#E8A820','#E8A820','#E24B4A','#E24B4A'],
      borderRadius:4,borderSkipped:false
    }]
  },
  options:{...defs}
});

new Chart(document.getElementById('revPieChart'),{
  type:'doughnut',
  data:{
    labels:['Rawat Inap','Rawat Jalan','IGD','Penunjang','Farmasi'],
    datasets:[{
      data:[198.45,143.28,67.94,52.3,25.33],
      backgroundColor:['#0FA896','#E8A820','#0B1A2E','#3B9B5A','#E24B4A'],
      borderWidth:2,borderColor:'transparent'
    }]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},cutout:'62%'}
});

const gm=[31.2,30.8,31.5,30.9,31.8,32.1,31.6,31.9,32.4,31.8,32.6,31.4];
const nm=[13.2,12.8,13.5,12.9,13.8,14.2,13.6,14.0,14.8,13.9,15.1,14.3];
new Chart(document.getElementById('marginTrendChart'),{
  type:'line',
  data:{
    labels:MONTHS,
    datasets:[
      {label:'Gross Margin',data:gm,borderColor:'#0FA896',backgroundColor:'rgba(15,168,150,.08)',fill:true,tension:.35,pointRadius:3,borderWidth:2},
      {label:'Net Margin',data:nm,borderColor:'#E8A820',backgroundColor:'rgba(232,168,32,.06)',fill:true,tension:.35,pointRadius:3,borderWidth:2,borderDash:[5,3]}
    ]
  },
  options:{...defs}
});

new Chart(document.getElementById('neracaChart'),{
  type:'bar',
  data:{
    labels:['Aset','Liabilitas & Ekuitas'],
    datasets:[
      {label:'Aset Lancar / Liab. Pendek',data:[221.6,120.5],backgroundColor:'#0FA896',borderRadius:4,borderSkipped:false},
      {label:'Aset Tdk Lancar / Liab. Panjang',data:[621,253.8],backgroundColor:'#0B1A2E',borderRadius:4,borderSkipped:false},
      {label:'Ekuitas',data:[0,468.3],backgroundColor:'#3B9B5A',borderRadius:4,borderSkipped:false}
    ]
  },
  options:{...defs,scales:{x:{stacked:true,grid:{color:gridColor()},ticks:{color:txtColor()}},y:{stacked:true,grid:{color:gridColor()},ticks:{color:txtColor(),callback:v=>v+'M'}}}}
});

new Chart(document.getElementById('revCabangChart'),{
  type:'bar',
  data:{
    labels:['Harapan Bunda','Mitra Sehat','Prima Medika','Sejahtera','Sentosa','Anugrah','Kartika'],
    datasets:[
      {label:'FY 2024',data:[98.4,87.6,80.2,70.4,64.8,52.8,33.1],backgroundColor:'#0FA896',borderRadius:4,borderSkipped:false},
      {label:'FY 2023',data:[87.2,79.1,72.1,67.6,58.2,46.5,27.7],backgroundColor:'rgba(136,153,170,.5)',borderRadius:4,borderSkipped:false}
    ]
  },
  options:{...defs}
});

const r24=[36.4,38.2,39.1,40.8,41.2,42.6,40.1,41.8,43.2,41.6,42.8,39.5];
const r23=[32.1,33.8,34.6,36.2,36.8,38.1,36.4,37.2,38.8,37.1,38.4,35.9];
new Chart(document.getElementById('revTrendChart'),{
  type:'line',
  data:{
    labels:MONTHS,
    datasets:[
      {label:'2024',data:r24,borderColor:'#0FA896',backgroundColor:'rgba(15,168,150,.1)',fill:true,tension:.35,pointRadius:3,borderWidth:2},
      {label:'2023',data:r23,borderColor:'rgba(136,153,170,.7)',backgroundColor:'transparent',fill:false,tension:.35,pointRadius:2,borderWidth:1.5,borderDash:[5,3]}
    ]
  },
  options:{...defs}
});

const ocf=[7.2,8.4,7.8,9.1,8.6,9.4,7.9,8.8,9.2,7.6,8.4,6.0];
const icf=[-3.2,-4.8,-6.2,-5.1,-4.6,-7.8,-5.4,-4.2,-8.6,-4.2,-5.8,-3.0];
const fcf=[-1.4,-1.8,-1.6,-1.8,-1.6,-1.8,-1.6,-1.8,-1.6,-1.8,-1.6,-0.6];
new Chart(document.getElementById('cfTrendChart'),{
  type:'bar',
  data:{
    labels:MONTHS,
    datasets:[
      {label:'Operasi',data:ocf,backgroundColor:'#0FA896',borderRadius:2,borderSkipped:false},
      {label:'Investasi',data:icf,backgroundColor:'#E24B4A',borderRadius:2,borderSkipped:false},
      {label:'Pendanaan',data:fcf,backgroundColor:'#E8A820',borderRadius:2,borderSkipped:false}
    ]
  },
  options:{...defs,scales:{x:{stacked:true,grid:{color:gridColor()},ticks:{color:txtColor()}},y:{stacked:true,grid:{color:gridColor()},ticks:{color:txtColor(),callback:v=>v+'M'}}}}
});
</script>

@endpush