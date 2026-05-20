@extends('layouts.main')
<style>
/* ═══════════════════════════════════════════
   DESIGN TOKENS — identik Balance Sheet Dashboard
═══════════════════════════════════════════ */
:root {
  --bg:      #F8F7F3;
  --bg2:     #EEEDE8;
  --white:   #FFFFFF;
  --ink:     #1A1714;
  --ink2:    #2D2A27;
  --ink3:    #433F3B;
  --muted:   #7A756E;
  --muted2:  #A09A93;
  --border:  rgba(26,23,20,0.08);
  --border2: rgba(26,23,20,0.14);
  --navy:    #1B3A6B; --navy2: #2952A3; --navy3: #D6E0F5;
  --forest:  #1D4A35; --forest2:#2E7554; --forest3:#D4EDE1;
  --rust:    #7A2020; --rust2:  #B83535; --rust3:  #F5DADA;
  --gold:    #6B4A00; --gold2:  #A87200; --gold3:  #F5E8C8;
  --plum:    #4A1D60; --plum2:  #7A35A0; --plum3:  #EAD8F5;
  --slate:   #1A3550; --slate2: #2B5580; --slate3: #D0E0F0;
  --terra:   #5C2D0E; --terra2: #8B4513; --terra3: #F0DDD0;
  --r: 12px; --r2: 8px;
  --shadow:  0 1px 4px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.05);
  --shadow2: 0 2px 8px rgba(0,0,0,0.09), 0 8px 28px rgba(0,0,0,0.08);
}
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
body { background:var(--bg); color:var(--ink); font-family:'Outfit',sans-serif; font-size:13px; line-height:1.5; }
::-webkit-scrollbar{width:5px;height:5px;}
::-webkit-scrollbar-track{background:var(--bg2);}
::-webkit-scrollbar-thumb{background:var(--border2);border-radius:3px;}

/* ══ LAYOUT ══ */
.layout { display:flex; min-height:100vh; }

/* ══ SIDEBAR ══ */
.sidebar {
  width:232px; flex-shrink:0; background:var(--ink2);
  display:flex; flex-direction:column;
  position:sticky; top:0; height:100vh; overflow-y:auto;
}
.sb-top { padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.07); }
.sb-logo { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.sb-logo-sq {
  width:34px; height:34px; border-radius:8px; flex-shrink:0;
  background:linear-gradient(135deg,#2952A3,#1B3A6B);
  display:flex; align-items:center; justify-content:center;
  font-family:'Fraunces',serif; font-size:16px; color:#fff; font-style:italic;
}
.sb-corp { font-size:11.5px; font-weight:600; color:#fff; line-height:1.3; }
.sb-corp-sub { font-size:10px; color:rgba(255,255,255,0.35); margin-top:2px; }
.sb-period-box {
  margin-top:10px; padding:9px 12px;
  background:rgba(255,255,255,0.06); border-radius:8px;
  border:1px solid rgba(255,255,255,0.09);
}
.sb-period-lbl { font-size:9px; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.3); margin-bottom:3px; }
.sb-period-val { font-size:11px; color:rgba(255,255,255,0.8); font-weight:500; }
.sb-period-cmp { font-size:10px; color:rgba(255,255,255,0.35); margin-top:1px; }

/* VIEW SWITCHER */
.sb-view { padding:12px 20px; border-bottom:1px solid rgba(255,255,255,0.07); }
.sb-view-lbl { font-size:9px; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.25); margin-bottom:7px; }
.sb-btns { display:flex; flex-direction:column; gap:4px; }
.sbv-btn {
  padding:7px 10px; font-size:11px; font-family:'Outfit',sans-serif;
  border-radius:6px; border:1px solid rgba(255,255,255,0.12);
  background:transparent; color:rgba(255,255,255,0.45); cursor:pointer;
  text-align:left; transition:all .15s; display:flex; align-items:center; gap:7px;
}
.sbv-btn.on { background:rgba(41,82,163,0.3); border-color:rgba(41,82,163,0.6); color:rgba(255,255,255,0.92); font-weight:500; }
.sbv-btn:hover:not(.on) { background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.7); }
.sbv-tag {
  font-size:9px; padding:1px 5px; border-radius:3px;
  background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.5);
  font-family:'JetBrains Mono',monospace; margin-left:auto;
}
.sbv-btn.on .sbv-tag { background:rgba(41,82,163,0.5); color:rgba(255,255,255,0.8); }

.sb-nav { padding:6px 0; flex:1; }
.sb-section { padding:12px 20px 4px; font-size:9px; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.22); }
.sb-item {
  display:flex; align-items:center; gap:9px;
  padding:7px 20px; color:rgba(255,255,255,0.45);
  cursor:pointer; font-size:12px; font-weight:400;
  border-left:2px solid transparent; transition:all .15s;
}
.sb-item:hover { color:rgba(255,255,255,0.75); background:rgba(255,255,255,0.04); }
.sb-item.on { color:#fff; background:rgba(41,82,163,0.22); border-left-color:#2952A3; font-weight:500; }
.sb-ic { font-size:12px; width:15px; text-align:center; flex-shrink:0; }

.sb-footer { padding:14px 20px; border-top:1px solid rgba(255,255,255,0.07); }
.sb-note {
  font-size:9.5px; color:rgba(255,255,255,0.25); margin-bottom:8px; line-height:1.5;
  padding:7px 9px; background:rgba(245,166,35,0.08);
  border:1px solid rgba(245,166,35,0.15); border-radius:6px; color:rgba(245,200,100,0.65);
}
.sb-print {
  width:100%; padding:7px; font-size:11px; font-family:'Outfit',sans-serif;
  background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.1);
  border-radius:6px; color:rgba(255,255,255,0.55); cursor:pointer; transition:all .15s;
}
.sb-print:hover { background:rgba(255,255,255,0.12); color:#fff; }

/* ══ MAIN ══ */
.main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.topbar {
  background:var(--white); border-bottom:1px solid var(--border);
  padding:11px 24px; display:flex; align-items:center; justify-content:space-between;
  position:sticky; top:0; z-index:10; box-shadow:0 1px 4px rgba(0,0,0,0.04);
}
.tb-left { display:flex; align-items:center; gap:12px; }
.tb-title-block .tb-title { font-size:14px; font-weight:600; color:var(--ink); }
.tb-title-block .tb-sub { font-size:11px; color:var(--muted2); margin-top:1px; }
.tb-right { display:flex; align-items:center; gap:7px; }
.chip {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
  font-family:'JetBrains Mono',monospace;
}
.chip.navy   { background:var(--navy3);   color:var(--navy);   }
.chip.green  { background:var(--forest3); color:var(--forest); }
.chip.gold   { background:var(--gold3);   color:var(--gold);   }
.chip.rust   { background:var(--rust3);   color:var(--rust2);  }
.chip.plum   { background:var(--plum3);   color:var(--plum);   }
.chip.slate  { background:var(--slate3);  color:var(--slate);  }
.chip.terra  { background:var(--terra3);  color:var(--terra);  }

.content { padding:20px 24px 40px; overflow-y:auto; flex:1; }

/* ══ VIEW SECTIONS ══ */
.view { display:none; }
.view.on { display:block; animation:rise .3s ease; }
@keyframes rise { from{opacity:0;transform:translateY(7px)} to{opacity:1;transform:translateY(0)} }

/* ══ SEC HEADER ══ */
.sec-hd { margin-bottom:18px; }
.sec-hd-title {
  font-family:'Fraunces',serif; font-size:25px; font-weight:300;
  color:var(--ink); letter-spacing:-0.02em; line-height:1.2;
}
.sec-hd-title em { font-style:italic; color:var(--navy2); }
.sec-hd-sub { font-size:12px; color:var(--muted); margin-top:4px; }
.sec-hd-dummy {
  display:inline-flex; align-items:center; gap:5px; margin-top:6px;
  padding:4px 10px; border-radius:4px; font-size:10px;
  background:var(--gold3); color:var(--gold); border:1px solid rgba(168,114,0,0.2);
  font-family:'JetBrains Mono',monospace;
}

/* ══ SECTION DIVIDER ══ */
.sec-div {
  display:grid; grid-template-columns:1fr auto 1fr;
  align-items:center; gap:10px; margin:20px 0 14px;
}
.sd-line { height:1px; background:var(--border2); }
.sd-txt {
  font-size:9.5px; text-transform:uppercase; letter-spacing:0.11em;
  color:var(--muted2); white-space:nowrap; padding:2px 8px;
  font-family:'JetBrains Mono',monospace;
  background:var(--bg2); border-radius:4px;
}

/* ══ KPI CARDS ══ */
.kpi-strip { display:grid; gap:12px; margin-bottom:14px; }
.kpi-6 { grid-template-columns:repeat(6,1fr); }
.kpi-4 { grid-template-columns:repeat(4,1fr); }
.kpi-3 { grid-template-columns:repeat(3,1fr); }

.kpi-card {
  background:var(--white); border:1px solid var(--border);
  border-radius:var(--r); padding:14px 15px;
  box-shadow:var(--shadow); overflow:hidden; position:relative;
  transition:box-shadow .2s, transform .15s;
}
.kpi-card:hover { box-shadow:var(--shadow2); transform:translateY(-1px); }
.kpi-accent { height:3px; border-radius:var(--r) var(--r) 0 0; position:absolute; top:0; left:0; right:0; }
.kpi-lbl { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted); margin-bottom:6px; }
.kpi-val { font-size:19px; font-weight:700; color:var(--ink); line-height:1; margin-bottom:5px; font-family:'Outfit',sans-serif; }
.kpi-val.big { font-size:22px; }
.kpi-meta { display:flex; align-items:center; gap:5px; flex-wrap:wrap; margin-bottom:3px; }
.kpi-yoy {
  display:flex; align-items:center; gap:5px; margin-top:6px;
  padding-top:6px; border-top:1px solid var(--border);
}
.kpi-yoy-lbl { font-size:9.5px; color:var(--muted2); }
.kpi-yoy-val { font-size:10px; font-family:'JetBrains Mono',monospace; font-weight:600; }
.yoy-up { color:var(--forest2); }
.yoy-dn { color:var(--rust2); }
.yoy-neu { color:var(--gold2); }

/* ══ PILL ══ */
.pill {
  display:inline-flex; align-items:center; gap:2px;
  font-size:10px; font-weight:600; padding:2px 7px; border-radius:12px;
  font-family:'JetBrains Mono',monospace;
}
.pill.up  { background:var(--forest3); color:var(--forest); }
.pill.dn  { background:var(--rust3);   color:var(--rust2);  }
.pill.neu { background:var(--gold3);   color:var(--gold);   }
.pill.inf { background:var(--navy3);   color:var(--navy);   }
.pill.plm { background:var(--plum3);   color:var(--plum);   }

/* ══ CARD ══ */
.card {
  background:var(--white); border:1px solid var(--border);
  border-radius:var(--r); overflow:hidden;
  box-shadow:var(--shadow); margin-bottom:14px;
  transition:box-shadow .2s;
}
.card:hover { box-shadow:var(--shadow2); }
.card-head {
  padding:11px 18px; border-bottom:1px solid var(--border);
  background:#FDFCFB; display:flex; align-items:center; justify-content:space-between;
  flex-wrap:wrap; gap:6px;
}
.card-title { font-size:12.5px; font-weight:600; color:var(--ink2); }
.card-sub   { font-size:10.5px; color:var(--muted2); margin-top:1px; }
.card-body  { padding:16px 18px; }
.card-body.nop { padding:0; }

/* ══ GRIDS ══ */
.g2  { display:grid; grid-template-columns:1fr 1fr;      gap:14px; }
.g3  { display:grid; grid-template-columns:1fr 1fr 1fr;  gap:14px; }
.g21 { display:grid; grid-template-columns:1.5fr 1fr;    gap:14px; }
.g12 { display:grid; grid-template-columns:1fr 1.5fr;    gap:14px; }
.g31 { display:grid; grid-template-columns:2fr 1fr;      gap:14px; }
.g13 { display:grid; grid-template-columns:1fr 2fr;      gap:14px; }
.g211{ display:grid; grid-template-columns:2fr 1fr 1fr;  gap:14px; }
.g321{ display:grid; grid-template-columns:3fr 2fr 1.5fr;gap:14px; }
.mb  { margin-bottom:14px; }

/* ══ TABLE ══ */
.tbl { width:100%; border-collapse:collapse; font-size:12px; }
.tbl thead tr { background:#F5F4F0; }
.tbl th {
  padding:9px 12px; font-size:10px; font-weight:600;
  color:var(--muted); text-transform:uppercase; letter-spacing:0.07em;
  border-bottom:1px solid var(--border2); text-align:left; white-space:nowrap;
  font-family:'JetBrains Mono',monospace;
}
.tbl th.r { text-align:right; }
.tbl td { padding:8.5px 12px; border-bottom:1px solid var(--border); color:var(--ink); vertical-align:middle; }
.tbl td.r { text-align:right; font-family:'JetBrains Mono',monospace; font-size:11.5px; }
.tbl td.g  { color:var(--forest); font-weight:600; }
.tbl td.r2 { color:var(--rust2); }
.tbl td.a  { color:var(--gold2); }
.tbl tr:last-child td { border-bottom:none; }
.tbl tr:hover td { background:var(--bg); }
.tbl tr.tot td {
  background:#EDF0F7; font-weight:700; font-family:'JetBrains Mono',monospace;
  border-top:1.5px solid var(--border2); font-size:11.5px;
}
.tbl tr.tot td:first-child { font-family:'Outfit',sans-serif; }
.tbl tr.grp td { background:#F0EDE8; font-weight:600; font-size:12px; color:var(--ink2); }

/* ══ BCHIP / BDOT ══ */
.bchip { display:flex; align-items:center; gap:7px; }
.bdot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ GAUGE ROW ══ */
.g-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.g-label { font-size:11px; color:var(--ink3); flex-shrink:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.g-bar { flex:1; background:var(--bg2); border-radius:4px; height:6px; overflow:hidden; }
.g-fill { height:100%; border-radius:4px; transition:width .7s cubic-bezier(.4,0,.2,1); }
.g-val { font-size:11px; font-family:'JetBrains Mono',monospace; color:var(--ink3); text-align:right; flex-shrink:0; }

/* ══ WATERFALL ══ */
.wf-row { display:flex; align-items:center; gap:10px; padding:5px 0; border-bottom:1px solid var(--border); }
.wf-row:last-child { border-bottom:none; }
.wf-label { font-size:11.5px; color:var(--ink3); width:155px; flex-shrink:0; }
.wf-track { flex:1; position:relative; height:22px; }
.wf-bar {
  height:22px; border-radius:4px; position:absolute; top:0; left:0;
  display:flex; align-items:center; padding:0 9px;
  font-size:10px; font-family:'JetBrains Mono',monospace; font-weight:700;
  min-width:10px; white-space:nowrap;
}
.wf-pos   { background:var(--forest3); color:var(--forest); }
.wf-neg   { background:var(--rust3);   color:var(--rust2);  }
.wf-total { background:var(--navy3);   color:var(--navy);   }
.wf-ebitda{ background:var(--gold3);   color:var(--gold2);  }
.wf-amount{ font-size:11px; font-family:'JetBrains Mono',monospace; color:var(--muted); width:95px; text-align:right; flex-shrink:0; }

/* ══ YOY BAR (comparison) ══ */
.yoy-bar-wrap {
  display:flex; align-items:center; gap:0;
  background:var(--bg2); border-radius:4px; height:6px;
  overflow:hidden; margin-top:4px;
}
.yoy-bar-prev { background:var(--border2); height:100%; }
.yoy-bar-curr { height:100%; }

/* ══ RATIO CARDS ══ */
.ratio-grid6 { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:14px; }
.ratio-grid4 { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:14px; }
.ratio-card {
  background:var(--white); border:1px solid var(--border);
  border-radius:var(--r2); padding:13px 12px;
  box-shadow:var(--shadow); text-align:center; border-top:3px solid;
}
.rc-lbl   { font-size:9.5px; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted); margin-bottom:5px; }
.rc-val   { font-size:20px; font-weight:700; font-family:'Outfit',sans-serif; line-height:1; margin-bottom:3px; }
.rc-desc  { font-size:10px; color:var(--muted2); margin-bottom:3px; }
.rc-bench { font-size:9.5px; font-family:'JetBrains Mono',monospace; color:var(--muted2); }

/* ══ TRAFFIC LIGHT ══ */
.tl-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.tl-item {
  display:flex; align-items:center; gap:10px;
  padding:9px 12px; border-radius:8px; border:1px solid var(--border);
  background:var(--white); transition:box-shadow .15s;
}
.tl-item:hover { box-shadow:var(--shadow); }
.tl-dot { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
.tl-green  { background:var(--forest2); box-shadow:0 0 6px rgba(46,117,84,0.4); }
.tl-amber  { background:var(--gold2);   box-shadow:0 0 6px rgba(168,114,0,0.4); }
.tl-red    { background:var(--rust2);   box-shadow:0 0 6px rgba(184,53,53,0.4); }
.tl-label  { font-size:11.5px; font-weight:500; color:var(--ink2); flex:1; }
.tl-value  { font-size:11px; font-family:'JetBrains Mono',monospace; color:var(--ink3); }
.tl-delta  { font-size:10px; font-family:'JetBrains Mono',monospace; }

/* ══ ALERT ITEMS ══ */
.alert-item {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 13px; margin-bottom:7px;
  border-radius:8px; border:1px solid transparent;
}
.alert-item.r { background:var(--rust3);   border-color:rgba(184,53,53,0.15); }
.alert-item.a { background:var(--gold3);   border-color:rgba(168,114,0,0.15); }
.alert-item.g { background:var(--forest3); border-color:rgba(46,117,84,0.15); }
.alert-item.b { background:var(--navy3);   border-color:rgba(41,82,163,0.18); }
.alert-icon { width:21px; height:21px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0; margin-top:1px; }
.ai-r { background:var(--rust2);   color:#fff; }
.ai-a { background:var(--gold2);   color:#fff; }
.ai-g { background:var(--forest2); color:#fff; }
.ai-b { background:var(--navy2);   color:#fff; }
.alert-title { font-size:12px; font-weight:600; color:var(--ink); margin-bottom:2px; }
.alert-desc  { font-size:11px; color:var(--ink3); line-height:1.55; }

/* ══ MINI BOX ══ */
.mini-box {
  background:var(--bg); border:1px solid var(--border);
  border-radius:var(--r2); padding:10px 12px; border-left:3px solid;
}
.mb-lbl { font-size:9.5px; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted); margin-bottom:3px; }
.mb-val { font-size:15px; font-weight:700; font-family:'Outfit',sans-serif; color:var(--ink); line-height:1; }
.mb-sub { font-size:10px; color:var(--muted2); margin-top:3px; font-family:'JetBrains Mono',monospace; }

/* ══ CASHFLOW STRIP ══ */
.cf-strip { display:flex; flex-direction:column; gap:8px; }
.cf-item {
  display:flex; align-items:center; gap:12px;
  padding:10px 14px; border-radius:8px; border:1px solid var(--border);
  background:var(--white);
}
.cf-icon { font-size:18px; flex-shrink:0; }
.cf-label { font-size:11.5px; font-weight:500; color:var(--ink2); flex:1; }
.cf-sub { font-size:10px; color:var(--muted); margin-top:1px; }
.cf-val { font-size:13px; font-weight:700; font-family:'JetBrains Mono',monospace; text-align:right; }
.cf-bar-outer { width:80px; background:var(--bg2); border-radius:3px; height:5px; overflow:hidden; }
.cf-bar-inner { height:100%; border-radius:3px; }

/* ══ HEALTH SCORE ══ */
.hs-ring {
  width:88px; height:88px; margin:0 auto 10px;
  border-radius:50%; position:relative;
  display:flex; align-items:center; justify-content:center;
}
.hs-ring::before { content:''; width:62px; height:62px; border-radius:50%; background:var(--white); position:absolute; z-index:1; }
.hs-num { position:relative; z-index:2; font-size:22px; font-weight:700; font-family:'Fraunces',serif; }

/* ══ LEGEND ══ */
.legend { display:flex; flex-wrap:wrap; gap:10px; }
.leg-i { display:flex; align-items:center; gap:5px; font-size:10.5px; color:var(--muted); }
.leg-sq { width:9px; height:9px; border-radius:2px; flex-shrink:0; }

canvas { display:block; }
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
                                Executive Overview
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



  <div class="content">

  <!-- ═══════════════════════════════════
       VIEW 1 — RINGKAS
  ═══════════════════════════════════ -->
  <div id="view-ringkas" class="view">

    <div class="sec-hd">
      <div class="sec-hd-title">Executive <em>Key Status</em> — Ringkas</div>
      <div class="sec-hd-sub">Kondisi keuangan perusahaan dalam satu pandangan · Konsolidasi PT. KAH + PT. JKK</div>
      <div class="sec-hd-dummy">⚠ Data YoY &amp; Cash Flow = Estimasi Dummy — Ganti dengan aktual</div>
    </div>

    <!-- KPI UTAMA 6 BOX -->
    <div class="kpi-strip kpi-6" id="r-kpi-top">
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--navy2)"></div>
      <div class="kpi-lbl">Total Revenue</div>
      <div class="kpi-val">0</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--forest2)"></div>
      <div class="kpi-lbl">Laba Bersih (EAT)</div>
      <div class="kpi-val">Rp 1.96M</div>
      <div class="kpi-meta"><span class="pill up">NPM 49.8%</span></div>
      <div class="kpi-yoy">
        <span class="kpi-yoy-lbl">vs Thn Lalu</span><span class="kpi-yoy-val yoy-up">▲11.3%</span>
        <span class="kpi-yoy-lbl" style="margin-left:auto;font-size:9.5px">1.76M (Des 2025)</span>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--gold2)"></div>
      <div class="kpi-lbl">EBITDA Konsolidasi</div>
      <div class="kpi-val">Rp 1.97M</div>
      <div class="kpi-meta"><span class="pill neu">Margin 49.9%</span></div>
      <div class="kpi-yoy">
        <span class="kpi-yoy-lbl">vs Thn Lalu</span><span class="kpi-yoy-val yoy-up">▲11.2%</span>
        <span class="kpi-yoy-lbl" style="margin-left:auto;font-size:9.5px">1.77M (Des 2025)</span>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--slate2)"></div>
      <div class="kpi-lbl">Total Aktiva</div>
      <div class="kpi-val">{!! $total_aktiva !!}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--rust2)"></div>
      <div class="kpi-lbl">Total Kewajiban</div>
      <div class="kpi-val">{!! $total_kewajiban !!}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--plum2)"></div>
      <div class="kpi-lbl">Arus Kas Operasi</div>
      <div class="kpi-val">Rp 1.93M</div>
      <div class="kpi-meta"><span class="pill plm">*Estimasi</span></div>
      <div class="kpi-yoy">
        <span class="kpi-yoy-lbl">vs Thn Lalu</span><span class="kpi-yoy-val yoy-up">▲10.2%</span>
        <span class="kpi-yoy-lbl" style="margin-left:auto;font-size:9.5px">1.75M (Des 2025)</span>
      </div>
    </div></div>

    <!-- ROW: Traffic Light + Cashflow Mini + Health -->
    <div class="g321 mb">

      <!-- Traffic Light Status -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">Status Indikator Keuangan</div>
            <div class="card-sub">Traffic light kondisi aktual vs benchmark</div>
          </div>
          <span class="chip navy">12 Indikator</span>
        </div>
        <div class="card-body">
          <div class="tl-grid" id="r-traffic-light">
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">Revenue YoY</div>
        <div style="font-size:10px;color:var(--muted2)">▲10.3%</div>
      </div>
      <div class="tl-value">3.94M</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">EBITDA YoY</div>
        <div style="font-size:10px;color:var(--muted2)">▲11.2%</div>
      </div>
      <div class="tl-value">1.97M</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">Laba Bersih YoY</div>
        <div style="font-size:10px;color:var(--muted2)">▲11.3%</div>
      </div>
      <div class="tl-value">1.96M</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">NPM</div>
        <div style="font-size:10px;color:var(--muted2)">Bench ≥20%</div>
      </div>
      <div class="tl-value">49.8%</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">GPM</div>
        <div style="font-size:10px;color:var(--muted2)">Bench ≥40%</div>
      </div>
      <div class="tl-value">74.9%</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">Current Ratio</div>
        <div style="font-size:10px;color:var(--muted2)">Bench ≥1.5x</div>
      </div>
      <div class="tl-value">1.49x</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-amber"></div>
      <div style="flex:1">
        <div class="tl-label">DAR</div>
        <div style="font-size:10px;color:var(--muted2)">Bench &lt;60%</div>
      </div>
      <div class="tl-value">54.6%</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-amber"></div>
      <div style="flex:1">
        <div class="tl-label">DER</div>
        <div style="font-size:10px;color:var(--muted2)">Bench &lt;1.0x</div>
      </div>
      <div class="tl-value">1.20x</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-red"></div>
      <div style="flex:1">
        <div class="tl-label">Piutang BPJS</div>
        <div style="font-size:10px;color:var(--muted2)">▲84% vs Nov</div>
      </div>
      <div class="tl-value">13.67M</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-amber"></div>
      <div style="flex:1">
        <div class="tl-label">Lab Bandung EAT</div>
        <div style="font-size:10px;color:var(--muted2)">Masih Rugi</div>
      </div>
      <div class="tl-value">-30.3Jt</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">Arus Kas Operasi</div>
        <div style="font-size:10px;color:var(--muted2)">*Estimasi</div>
      </div>
      <div class="tl-value">1.93M</div>
    </div>
    <div class="tl-item">
      <div class="tl-dot tl-green"></div>
      <div style="flex:1">
        <div class="tl-label">FCF</div>
        <div style="font-size:10px;color:var(--muted2)">*Estimasi</div>
      </div>
      <div class="tl-value">1.24M</div>
    </div></div>
        </div>
      </div>

      <!-- Cash Flow Mini -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Arus Kas Ringkas</div>
          <div class="card-sub">YTD Des 2026 · *Estimasi</div>
        </div>
        <div class="card-body" style="padding:14px">
          <div class="cf-strip" id="r-cf-strip"><div class="cf-item">
      <div class="cf-icon">⚙️</div>
      <div style="flex:1">
        <div class="cf-label">Arus Kas Operasi</div>
        <div style="font-size:10px;color:var(--muted)">Penerimaan pasien &amp; pembayaran beban</div>
        <div style="margin-top:4px;height:4px;background:var(--bg2);border-radius:2px;overflow:hidden">
          <div style="width:100%;height:100%;border-radius:2px;background:var(--forest2)"></div>
        </div>
      </div>
      <div class="cf-val" style="color:var(--forest2)">1.93M</div>
    </div><div class="cf-item">
      <div class="cf-icon">🏗️</div>
      <div style="flex:1">
        <div class="cf-label">Arus Kas Investasi</div>
        <div style="font-size:10px;color:var(--muted)">Pembelian aset tetap &amp; alat medis</div>
        <div style="margin-top:4px;height:4px;background:var(--bg2);border-radius:2px;overflow:hidden">
          <div style="width:35.61430793157076%;height:100%;border-radius:2px;background:var(--rust2)"></div>
        </div>
      </div>
      <div class="cf-val" style="color:var(--rust2)">-687.0Jt</div>
    </div><div class="cf-item">
      <div class="cf-icon">🏦</div>
      <div style="flex:1">
        <div class="cf-label">Arus Kas Pendanaan</div>
        <div style="font-size:10px;color:var(--muted)">Cicilan hutang bank jangka panjang</div>
        <div style="margin-top:4px;height:4px;background:var(--bg2);border-radius:2px;overflow:hidden">
          <div style="width:16.174183514774494%;height:100%;border-radius:2px;background:var(--rust2)"></div>
        </div>
      </div>
      <div class="cf-val" style="color:var(--rust2)">-312.0Jt</div>
    </div></div>
          <div style="margin-top:12px;padding:10px;background:var(--forest3);border-radius:8px;border:1px solid rgba(46,117,84,0.2)">
            <div style="font-size:10px;color:var(--forest);font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px">Free Cash Flow</div>
            <div style="font-size:18px;font-weight:700;color:var(--forest2);font-family:&#39;Fraunces&#39;,serif">Rp 1,24 M</div>
            <div style="font-size:10px;color:var(--forest);margin-top:2px">Operasi − Investasi · FCF Margin 31.4%</div>
          </div>
        </div>
      </div>

      <!-- Health + Rasio -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Financial Health</div>
        </div>
        <div class="card-body" style="padding:12px">
          <div style="text-align:center;margin-bottom:12px">
            <div class="hs-ring" style="background:conic-gradient(var(--forest2) 0% 72%, var(--bg2) 72% 100%)">
              <div class="hs-num" style="color:var(--forest2)">72</div>
            </div>
            <div style="font-size:13px;font-weight:700;color:var(--forest);margin-bottom:2px">SEHAT</div>
            <div style="font-size:10px;color:var(--muted);line-height:1.5">Profit excellent · Leverage tinggi<br>Piutang BPJS perlu akselerasi</div>
          </div>
          <div style="display:flex;flex-direction:column;gap:5px" id="r-rasio-mini">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">NPM</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:12px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">49.8%</span>
        <span class="pill up">✓</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">EBITDA%</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:12px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">49.9%</span>
        <span class="pill up">✓</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">CR</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:12px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">1.49x</span>
        <span class="pill up">✓</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">DER</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:12px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--rust2)">1.20x</span>
        <span class="pill dn">⚠</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">GPM</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:12px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">74.9%</span>
        <span class="pill up">✓</span>
      </div>
    </div></div>
        </div>
      </div>
    </div>

    <!-- YoY Comparison Bar + Scorecard Mini + Alerts -->
    <div class="sec-div">
      <div class="sd-line"></div><div class="sd-txt">Perbandingan YoY &amp; Kinerja Per Cabang</div><div class="sd-line"></div>
    </div>

    <div class="g211 mb">
      <!-- YoY Chart -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">Perbandingan YoY — Revenue, EBITDA, Laba Bersih</div>
            <div class="card-sub">YTD Des 2026 vs YTD Des 2025 · *Des 2025 = Estimasi</div>
          </div>
          <div style="display:flex;gap:6px">
            <span class="chip green">+9.8% Rev</span>
            <span class="chip navy">+8.2% EBITDA</span>
          </div>
        </div>
        <div class="card-body">
          <div style="position:relative;height:190px"><canvas id="r-c-yoy" width="765" height="190" style="display: block; box-sizing: border-box; height: 190px; width: 765px;"></canvas></div>
        </div>
      </div>

      <!-- Scorecard Mini -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Scorecard Cabang — Ringkas</div>
        </div>
        <div class="card-body nop">
          <table class="tbl" id="r-sc-mini">
            <thead><tr>
              <th style="padding-left:12px">Cabang</th>
              <th class="r">Revenue</th>
              <th class="r">NPM</th>
              <th class="r" style="padding-right:12px">Status</th>
            </tr></thead>
            <tbody id="r-sc-body"><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#1B3A6B"></span>Tasik</span></td>
      <td class="r">1.79M</td>
      <td class="r"><span class="pill up">47.7%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#1A3550"></span>Lab Sub</span></td>
      <td class="r">722.8Jt</td>
      <td class="r"><span class="pill up">71.5%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#1D4A35"></span>Banjar</span></td>
      <td class="r">659.1Jt</td>
      <td class="r"><span class="pill inf">38.8%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#6B4A00"></span>Ciamis</span></td>
      <td class="r">358.3Jt</td>
      <td class="r"><span class="pill up">83.8%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#7A2020"></span>Lab BDG</span></td>
      <td class="r">247.3Jt</td>
      <td class="r"><span class="pill dn">-12.3%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill dn">Rugi</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#3B6E2B"></span>Apotek</span></td>
      <td class="r">160.3Jt</td>
      <td class="r"><span class="pill up">42.8%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#4A1D60"></span>Subang</span></td>
      <td class="r">7.4Jt</td>
      <td class="r"><span class="pill inf">19.4%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill up">Profit</span></td>
    </tr><tr>
      <td style="padding-left:12px"><span class="bchip"><span class="bdot" style="background:#64748B"></span>HO</span></td>
      <td class="r">555rb</td>
      <td class="r"><span class="pill dn">-16.0%</span></td>
      <td class="r" style="padding-right:12px"><span class="pill dn">Rugi</span></td>
    </tr></tbody>
          </table>
        </div>
      </div>

      <!-- Alerts Ringkas -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Alerts Prioritas</div>
        </div>
        <div class="card-body" style="padding:12px" id="r-alerts">
    <div class="alert-item r" style="margin-bottom:6px">
      <div class="alert-icon ai-r">!</div>
      <div><div class="alert-title">Piutang BPJS ▲84% — Rp 13,67 M</div><div class="alert-desc">Naik dari Rp 7,42M (Nov) → Rp 13,67M (Des 2025). Akselerasi submit klaim BPJS segera untuk jaga likuiditas operasional.</div></div>
    </div>
    <div class="alert-item r" style="margin-bottom:6px">
      <div class="alert-icon ai-r">!</div>
      <div><div class="alert-title">Lab Bandung EAT Negatif</div><div class="alert-desc">SDM Rp 160,5Jt vs Revenue Rp 247Jt. OpEx ratio 86.3%. Review struktur SDM dan agresifkan revenue development.</div></div>
    </div>
    <div class="alert-item a" style="margin-bottom:6px">
      <div class="alert-icon ai-a">~</div>
      <div><div class="alert-title">Hutang Bank JP Rp 79,5M — DER 1.22x</div><div class="alert-desc">70.6% total hutang adalah hutang bank JP. Susun roadmap deleveraging yang terstruktur seiring kenaikan profitabilitas.</div></div>
    </div>
    <div class="alert-item a" style="margin-bottom:6px">
      <div class="alert-icon ai-a">~</div>
      <div><div class="alert-title">Aset Tetap 78.8% Terdepresiasi</div><div class="alert-desc">Nilai buku bersih hanya 21.2%. Perencanaan capex peremajaan alat medis dalam 1-2 tahun ke depan diprioritaskan.</div></div>
    </div>
    <div class="alert-item g" style="margin-bottom:6px">
      <div class="alert-icon ai-g">+</div>
      <div><div class="alert-title">NPM 48.7% &amp; EBITDA 48.9%</div><div class="alert-desc">Jauh di atas benchmark industri RS 18-22%. Operasional grup sangat profitable dan efisien biaya secara keseluruhan.</div></div>
    </div>
    <div class="alert-item g" style="margin-bottom:6px">
      <div class="alert-icon ai-g">+</div>
      <div><div class="alert-title">Revenue YoY ▲9.8%, EAT ▲12.3%</div><div class="alert-desc">Pertumbuhan organik yang solid. EAT tumbuh lebih cepat dari revenue — menandakan efisiensi operasional membaik.</div></div>
    </div></div>
      </div>
    </div>

  </div><!-- /view-ringkas -->


  <!-- ═══════════════════════════════════
       VIEW 2 — LENGKAP
  ═══════════════════════════════════ -->
  <div id="view-lengkap" class="view on">

    <!-- KPI UTAMA 6 BOX -->
    <div class="kpi-strip kpi-6" id="l-kpi-top">
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--navy2)"></div>
      <div class="kpi-lbl">Total Revenue</div>
      <div class="kpi-val">0</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--forest2)"></div>
      <div class="kpi-lbl">Laba Bersih (EAT)</div>
      <div class="kpi-val">0</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--gold2)"></div>
      <div class="kpi-lbl">EBITDA Konsolidasi</div>
      <div class="kpi-val">0</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--slate2)"></div>
      <div class="kpi-lbl">Total Aktiva</div>
      <div class="kpi-val">{!! $total_aktiva !!}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-accent" style="background:var(--rust2)"></div>
      <div class="kpi-lbl">Total Kewajiban</div>
      <div class="kpi-val">{!! $total_kewajiban !!}</div>
    </div>
    <!-- div class="kpi-card">
      <div class="kpi-accent" style="background:var(--plum2)"></div>
      <div class="kpi-lbl">Arus Kas Operasi</div>
      <div class="kpi-val">Rp 1.93M</div>
      <div class="kpi-meta"><span class="pill plm">*Estimasi</span></div>
      <div class="kpi-yoy">
        <span class="kpi-yoy-lbl">vs Thn Lalu</span><span class="kpi-yoy-val yoy-up">▲10.2%</span>
        <span class="kpi-yoy-lbl" style="margin-left:auto;font-size:9.5px">1.75M (Des 2025)</span>
      </div>
    </div -->
    </div>

    <!-- SEC: P&L -->
    <div class="sec-div"><div class="sd-line"></div><div class="sd-txt">Profitabilitas — Income Statement YTD Des 2026</div><div class="sd-line"></div></div>

    <!-- Waterfall + YoY Bar + Revenue Donut -->
    <div class="g321 mb">
      <!-- Waterfall -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">Waterfall Laba Rugi Konsolidasi</div>
            <div class="card-sub">Revenue → EAT · YTD Des 2026 (Rp)</div>
          </div>
          <span class="chip gold">P&amp;L</span>
        </div>
        <div class="card-body" id="l-wf"><div class="wf-row">
      <div class="wf-label">Revenue Bersih</div>
      <div class="wf-track"><div class="wf-bar wf-pos" style="width:100%">3.94M</div></div>
      <div class="wf-amount">3.944.343.631</div>
    </div><div class="wf-row">
      <div class="wf-label">(−) HPP / BPP</div>
      <div class="wf-track"><div class="wf-bar wf-neg" style="width:25.14675620790031%">-991.9Jt</div></div>
      <div class="wf-amount">(991.874.477)</div>
    </div><div class="wf-row">
      <div class="wf-label">Laba Kotor</div>
      <div class="wf-track"><div class="wf-bar wf-total" style="width:74.85324379209969%">2.95M</div></div>
      <div class="wf-amount">2.952.469.154</div>
    </div><div class="wf-row">
      <div class="wf-label">(−) Biaya GA</div>
      <div class="wf-track"><div class="wf-bar wf-neg" style="width:24.90508337711258%">-982.3Jt</div></div>
      <div class="wf-amount">(982.342.070)</div>
    </div><div class="wf-row">
      <div class="wf-label">EBITDA</div>
      <div class="wf-track"><div class="wf-bar wf-ebitda" style="width:49.948160414987115%">1.97M</div></div>
      <div class="wf-amount">1.970.127.084</div>
    </div><div class="wf-row">
      <div class="wf-label">(−) Penyusutan</div>
      <div class="wf-track"><div class="wf-bar wf-neg" style="width:0.15430323949335406%">-6.1Jt</div></div>
      <div class="wf-amount">(6.086.250)</div>
    </div><div class="wf-row">
      <div class="wf-label">Laba Bersih (EAT)</div>
      <div class="wf-track"><div class="wf-bar wf-total" style="width:49.79385717549376%">1.96M</div></div>
      <div class="wf-amount">1.964.040.834</div>
    </div></div>
      </div>

      <!-- YoY Comparison -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">YoY — Des 2026 vs Des 2025</div>
            <div class="card-sub">*Des 2025 = estimasi dummy</div>
          </div>
          <span class="chip slate">YoY</span>
        </div>
        <div class="card-body">
          <div style="position:relative;height:165px"><canvas id="l-c-yoy" style="box-sizing: border-box; display: block; height: 165px; width: 456px;" width="456" height="165"></canvas></div>
          <div style="margin-top:10px" id="l-yoy-detail"><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">Revenue</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:10px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">3.58M →</span>
        <span style="font-size:11px;font-weight:700;color:var(--ink);font-family:&#39;JetBrains Mono&#39;,monospace">3.94M</span>
        <span class="pill up">▲10.3%</span>
      </div>
    </div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">Laba Kotor</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:10px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">2.66M →</span>
        <span style="font-size:11px;font-weight:700;color:var(--ink);font-family:&#39;JetBrains Mono&#39;,monospace">2.95M</span>
        <span class="pill up">▲10.8%</span>
      </div>
    </div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">EBITDA</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:10px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">1.77M →</span>
        <span style="font-size:11px;font-weight:700;color:var(--ink);font-family:&#39;JetBrains Mono&#39;,monospace">1.97M</span>
        <span class="pill up">▲11.2%</span>
      </div>
    </div><div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">EAT</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:10px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">1.76M →</span>
        <span style="font-size:11px;font-weight:700;color:var(--ink);font-family:&#39;JetBrains Mono&#39;,monospace">1.96M</span>
        <span class="pill up">▲11.3%</span>
      </div>
    </div></div>
        </div>
      </div>

      <!-- Revenue Donut -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Revenue per Entitas</div>
          <div class="card-sub">Distribusi kontribusi</div>
        </div>
        <div class="card-body">
          <div style="position:relative;height:145px">
            <canvas id="l-c-rev-donut" style="box-sizing: border-box; display: block; height: 145px; width: 332px;" width="332" height="145"></canvas>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none">
              <div style="font-family:&#39;Fraunces&#39;,serif;font-size:13px;font-weight:600;color:var(--ink)">Rp 3,94M</div>
              <div style="font-size:9px;color:var(--muted)">Total</div>
            </div>
          </div>
          <div class="legend" style="margin-top:8px;justify-content:center" id="l-rev-legend"><span class="leg-i"><span class="leg-sq" style="background:#1B3A6B"></span>Tasik <span style="color:var(--muted2)">45.3%</span></span><span class="leg-i"><span class="leg-sq" style="background:#1D4A35"></span>Banjar <span style="color:var(--muted2)">16.7%</span></span><span class="leg-i"><span class="leg-sq" style="background:#6B4A00"></span>Ciamis <span style="color:var(--muted2)">9.1%</span></span><span class="leg-i"><span class="leg-sq" style="background:#1A3550"></span>Lab Sub <span style="color:var(--muted2)">18.3%</span></span><span class="leg-i"><span class="leg-sq" style="background:#7A2020"></span>Lab BDG <span style="color:var(--muted2)">6.3%</span></span><span class="leg-i"><span class="leg-sq" style="background:#3B6E2B"></span>Apotek <span style="color:var(--muted2)">4.1%</span></span></div>
        </div>
      </div>
    </div>

    <!-- Scorecard Table Lengkap -->
    <div class="card mb">
      <div class="card-head">
        <div>
          <div class="card-title">Scorecard P&amp;L Lengkap per Entitas — YTD Des 2026</div>
          <div class="card-sub">Revenue, Laba Kotor, EBITDA, EAT beserta perubahan YoY per entitas</div>
        </div>
        <div style="display:flex;gap:6px">
          <span class="chip green">Income Statement</span>
          <span class="chip navy">8 Entitas</span>
        </div>
      </div>
      <div class="card-body nop">
        <table class="tbl">
          <thead><tr>
            <th style="padding-left:13px;width:18%">Entitas</th>
            <th class="r">Revenue</th>
            <th class="r">YoY Rev</th>
            <th class="r">Laba Kotor</th>
            <th class="r">GPM</th>
            <th class="r">EBITDA</th>
            <th class="r">EBITDA%</th>
            <th class="r">YoY EBITDA</th>
            <th class="r">EAT</th>
            <th class="r" style="padding-right:13px">NPM</th>
          </tr></thead>
          <tbody id="l-sc-body"><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#1B3A6B"></span>Klinik Tasik</span></td>
      <td class="r">1.788.672.528</td>
      <td class="r"><span class="pill up">▲9.9%</span></td>
      <td class="r g">1.238.878.801</td>
      <td class="r"><span class="pill up">69.3%</span></td>
      <td class="r g">852.322.421</td>
      <td class="r"><span class="pill up">47.7%</span></td>
      <td class="r"><span class="pill up">▲12.1%</span></td>
      <td class="r g">852.322.421</td>
      <td class="r" style="padding-right:13px"><span class="pill up">47.7%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#1A3550"></span>Lab Subang</span></td>
      <td class="r">722.757.650</td>
      <td class="r"><span class="pill up">▲9.8%</span></td>
      <td class="r g">582.489.862</td>
      <td class="r"><span class="pill up">80.6%</span></td>
      <td class="r g">516.439.394</td>
      <td class="r"><span class="pill up">71.5%</span></td>
      <td class="r"><span class="pill up">▲9.9%</span></td>
      <td class="r g">516.439.394</td>
      <td class="r" style="padding-right:13px"><span class="pill up">71.5%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#1D4A35"></span>Klinik Banjar</span></td>
      <td class="r">659.083.150</td>
      <td class="r"><span class="pill up">▲10.0%</span></td>
      <td class="r g">481.473.949</td>
      <td class="r"><span class="pill up">73.1%</span></td>
      <td class="r g">261.488.102</td>
      <td class="r"><span class="pill inf">39.7%</span></td>
      <td class="r"><span class="pill up">▲9.4%</span></td>
      <td class="r g">255.401.852</td>
      <td class="r" style="padding-right:13px"><span class="pill inf">38.8%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#6B4A00"></span>Klinik Ciamis</span></td>
      <td class="r">358.256.050</td>
      <td class="r"><span class="pill up">▲12.0%</span></td>
      <td class="r g">341.199.175</td>
      <td class="r"><span class="pill up">95.2%</span></td>
      <td class="r g">300.129.296</td>
      <td class="r"><span class="pill up">83.8%</span></td>
      <td class="r"><span class="pill up">▲12.2%</span></td>
      <td class="r g">300.129.296</td>
      <td class="r" style="padding-right:13px"><span class="pill up">83.8%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#7A2020"></span>Lab Bandung</span></td>
      <td class="r">247.266.488</td>
      <td class="r"><span class="pill up">▲9.9%</span></td>
      <td class="r g">182.783.745</td>
      <td class="r"><span class="pill up">73.9%</span></td>
      <td class="r r2">(30.299.043)</td>
      <td class="r"><span class="pill dn">-12.3%</span></td>
      <td class="r"><span class="pill dn">▼10.2%</span></td>
      <td class="r r2">(30.299.043)</td>
      <td class="r" style="padding-right:13px"><span class="pill dn">-12.3%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#3B6E2B"></span>Apotek Subang</span></td>
      <td class="r">160.346.364</td>
      <td class="r"><span class="pill up">▲9.8%</span></td>
      <td class="r g">118.785.571</td>
      <td class="r"><span class="pill up">74.1%</span></td>
      <td class="r g">68.702.104</td>
      <td class="r"><span class="pill up">42.8%</span></td>
      <td class="r"><span class="pill up">▲10.8%</span></td>
      <td class="r g">68.702.104</td>
      <td class="r" style="padding-right:13px"><span class="pill up">42.8%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#4A1D60"></span>Klinik Subang</span></td>
      <td class="r">7.406.800</td>
      <td class="r"><span class="pill up">▲0.0%</span></td>
      <td class="r g">6.896.800</td>
      <td class="r"><span class="pill up">93.1%</span></td>
      <td class="r g">1.433.700</td>
      <td class="r"><span class="pill inf">19.4%</span></td>
      <td class="r"><span class="pill up">▲0.0%</span></td>
      <td class="r g">1.433.700</td>
      <td class="r" style="padding-right:13px"><span class="pill inf">19.4%</span></td>
    </tr><tr>
      <td style="padding-left:13px"><span class="bchip"><span class="bdot" style="background:#64748B"></span>HO / PT.JKK</span></td>
      <td class="r">554.601</td>
      <td class="r"><span class="pill dn">▼8.2%</span></td>
      <td class="r r2">(38.748)</td>
      <td class="r"><span class="pill dn">-7.0%</span></td>
      <td class="r r2">(88.889)</td>
      <td class="r"><span class="pill dn">-16.0%</span></td>
      <td class="r"><span class="pill dn">▼1087.7%</span></td>
      <td class="r r2">(88.889)</td>
      <td class="r" style="padding-right:13px"><span class="pill dn">-16.0%</span></td>
    </tr><tr class="tot">
    <td style="padding-left:13px">Konsolidasi</td>
    <td class="r">3.944.343.631</td>
    <td class="r"><span class="pill up">▲10.3%</span></td>
    <td class="r">2.952.469.154</td>
    <td class="r"><span class="pill up">74.9%</span></td>
    <td class="r">1.970.127.084</td>
    <td class="r"><span class="pill up">49.9%</span></td>
    <td class="r"><span class="pill up">▲11.2%</span></td>
    <td class="r">1.964.040.834</td>
    <td class="r" style="padding-right:13px"><span class="pill up">49.8%</span></td>
  </tr></tbody>
        </table>
      </div>
    </div>

    <!-- SEC: NERACA -->
    <div class="sec-div"><div class="sd-line"></div><div class="sd-txt">Neraca &amp; Posisi Keuangan — 31 Desember 2025</div><div class="sd-line"></div></div>

    <!-- KPI Neraca -->
    <div class="kpi-strip kpi-4 mb" id="l-kpi-neraca"><div class="kpi-card">
    <div class="kpi-accent" style="background:var(--navy2)"></div>
    <div class="kpi-lbl">Total Aktiva</div>
    <div class="kpi-val">{!! $total_aktiva !!}</div>
  </div><div class="kpi-card">
    <div class="kpi-accent" style="background:var(--forest2)"></div>
    <div class="kpi-lbl">Aktiva Lancar</div>
    <div class="kpi-val">{!! $dash_total_asset_lancar!!}</div>
  </div><div class="kpi-card">
    <div class="kpi-accent" style="background:var(--rust2)"></div>
    <div class="kpi-lbl">Total Kewajiban</div>
    <div class="kpi-val">{!! $total_kewajiban !!}</div>
  </div><div class="kpi-card">
    <div class="kpi-accent" style="background:var(--plum2)"></div>
    <div class="kpi-lbl">Total Ekuitas</div>
    <div class="kpi-val">{!! $total_ekuitas !!}</div>
  </div></div>

    <!-- Neraca Ringkas + Piutang + Rasio -->
    <div class="g31 mb">
      <!-- Aktiva vs Pasiva bars -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">Struktur Aktiva &amp; Pasiva</div>
            <div class="card-sub">Konsolidasi · {!! $bulan_nama !!}  {!! $tahun !!} (Rp)</div>
          </div>
          <span class="chip navy">Balance Sheet</span>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
              <div style="font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:var(--navy2);font-family:&#39;JetBrains Mono&#39;,monospace;margin-bottom:8px;border-left:2px solid var(--navy2);padding-left:6px">AKTIVA</div>
              <div id="l-aktiva-bars">

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
                                    '#BDC3C7',  // 9. Silver/Grey (Lainnya - Akumulasi)
                                    '#16A085', // 10. Dark Teal / Emerald (Lain-lain 3)
                                    '#E84393', // 11. Pink / Magenta (Kewajiban / Modal)
                                    '#D35400'  // 12. Dark Orange / Burnt Sienna (Biaya / Pengeluaran)
                                ];

                            $total_aktiva = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? 1;

                        @endphp

                        @foreach($bar_aktiva as $index => $item)
                            @php
                                // Hapus &nbsp; dan spasi liar
                                $nama_bersih = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
                                $nilai       = $item['amounts']['total']['amount_aktiva'];
                                $nilaiformat = str_replace('Rp. ','',formatKeJT($item['amounts']['total']['amount_aktiva']));
                                $persen      = round(($nilai / $total_aktiva) * 100 ,2);
                                if($nilai  < 0 ) $persen = 0;
                            @endphp

                            <div class="g-row">
                              <div class="g-label" style="width:220px;font-size:10.5px">{{ $nama_bersih }} </div>
                              <div class="g-bar"><div class="g-fill" style="width:{{ $persen }}%; background:{{ $list_warna[$index] ?? '#cccccc' }}"></div></div>
                              <div class="g-val" style="width:65px;font-size:10px">{{ $nilaiformat }}</div>
                            </div>

                        @endforeach
            </div>
            </div>
            <div>
              <div style="font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:var(--forest2);font-family:&#39;JetBrains Mono&#39;,monospace;margin-bottom:8px;border-left:2px solid var(--forest2);padding-left:6px">PASIVA</div>
              <div id="l-pasiva-bars">


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
                          '#BDC3C7',  // 9. Silver/Grey (Lainnya - Akumulasi)
                          '#16A085', // 10. Dark Teal / Emerald (Lain-lain 3)
                          '#E84393', // 11. Pink / Magenta (Kewajiban / Modal)
                          '#D35400'  // 12. Dark Orange / Burnt Sienna (Biaya / Pengeluaran)
                      ];

                  $total_pasiva = $data_pos[1][14]['amounts']['total']['amount_aktiva'] ?? 1;

              @endphp

              @foreach($bar_pasiva as $index => $item)
                  @php
                      // Hapus &nbsp; dan spasi liar
                      $nama_bersih = trim(str_replace('&nbsp;', '', strip_tags($item['nama_pos'])));
                      $nilai = $item['amounts']['total']['amount_aktiva'];
                      $nilaiformat = str_replace('Rp. ','',formatKeJT($item['amounts']['total']['amount_aktiva']));
                      $persen_pasiva = round(($nilai / $total_pasiva) * 100 ,2);

                      if($nilai  < 0 ) $persen_pasiva = 0;

                  @endphp


                        <div class="g-row">
                          <div class="g-label" style="width:220px;font-size:10.5px">{{ $nama_bersih }}</div>
                          <div class="g-bar"><div class="g-fill" style="width:{{ $persen_pasiva }}%;background:{{ $list_warna[$index] ?? '#cccccc' }}"></div></div>
                          <div class="g-val" style="width:65px;font-size:10px">{{ $nilaiformat }}</div>
                        </div>

              @endforeach
            </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Piutang -->
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">Komposisi Piutang</div>
            <div class="card-sub">Total Rp 14,97 M — BPJS 91.3% ⚠</div>
          </div>
          <span class="chip gold">Watch</span>
        </div>
        <div class="card-body">
          <div style="position:relative;height:120px"><canvas id="l-c-piutang" style="box-sizing: border-box; display: block; height: 120px; width: 363px;" width="363" height="120"></canvas></div>
          <div style="margin-top:10px" id="l-piutang-bars"><div class="g-row">
      <div class="g-label" style="width:130px;font-size:10.5px">BPJS Kesehatan</div>
      <div class="g-bar"><div class="g-fill" style="width:100%;background:#1B3A6B"></div></div>
      <div class="g-val" style="font-size:10px">13.67M <span style="color:var(--muted2)">79.1%</span></div>
    </div><div class="g-row">
      <div class="g-label" style="width:130px;font-size:10.5px">Piutang Lain-Lain</div>
      <div class="g-bar"><div class="g-fill" style="width:17.330015238552658%;background:#4A1D60"></div></div>
      <div class="g-val" style="font-size:10px">2.37M <span style="color:var(--muted2)">13.7%</span></div>
    </div><div class="g-row">
      <div class="g-label" style="width:130px;font-size:10.5px">Pasien Perawatan</div>
      <div class="g-bar"><div class="g-fill" style="width:4.3066343584986715%;background:#1D4A35"></div></div>
      <div class="g-val" style="font-size:10px">588.9Jt <span style="color:var(--muted2)">3.4%</span></div>
    </div><div class="g-row">
      <div class="g-label" style="width:130px;font-size:10.5px">Asuransi/Perusahaan</div>
      <div class="g-bar"><div class="g-fill" style="width:2.5927198666429287%;background:#6B4A00"></div></div>
      <div class="g-val" style="font-size:10px">354.5Jt <span style="color:var(--muted2)">2.1%</span></div>
    </div></div>
        </div>
      </div>
    </div>
        <div class="g211 mb">

      <!-- Rasio -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Rasio Keuangan Kunci</div>
          <div class="card-sub">vs Benchmark Industri RS</div>
        </div>
        <div class="card-body" style="padding:12px">
          <div id="l-rasio-list">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">Current Ratio</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">{!! $curr_ratio !!}</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">DAR</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">{!! $dta !!} %</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">DER</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--rust2)">{!! $dte !!}x</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">NPM</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:9.5px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">≥20%</span>
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">49.8%</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">EBITDA Margin</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:9.5px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">≥20%</span>
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">49.9%</span>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border)">
      <span style="font-size:11px;color:var(--ink3)">GPM</span>
      <div style="display:flex;align-items:center;gap:6px">
        <span style="font-size:9.5px;color:var(--muted2);font-family:&#39;JetBrains Mono&#39;,monospace">≥40%</span>
        <span style="font-size:13px;font-weight:700;font-family:&#39;Outfit&#39;,sans-serif;color:var(--forest2)">74.9%</span>
      </div>
    </div></div>
        </div>
      </div>
    </div>



  </div><!-- /view-lengkap -->

  </div><!-- /content -->










        </div>
    </div>
</div>
@endsection

@push('script')
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

        let $link = "{{ route('dashboard.overview', []) }}"
        window.location.replace($link + '?' + $param);
        return false
    });



</script>

@endpush