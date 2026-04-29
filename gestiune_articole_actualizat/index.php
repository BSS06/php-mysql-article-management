<?php
require_once __DIR__.'/auth.php';
necesita_autentificare();
$user = utilizator_curent();
$rol  = rol_curent();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Portal Editorial</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
:root{--sb:#12151f;--sb2:#1a1e2e;--sb3:#242840;--acc:#7c6fcd;--bg:#f0f2f8;--card:#fff;--tx:#1a1a2e;--tx2:#5a5a7a;--tx3:#9090a8;--bd:#e4e6f0;--r:10px}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:var(--bg);color:var(--tx);display:flex;height:100vh;overflow:hidden}

.sb{width:255px;min-width:255px;background:var(--sb);display:flex;flex-direction:column;height:100vh}
.brand{padding:18px 16px 14px;border-bottom:1px solid #1f2235;flex-shrink:0}
.bmark{width:34px;height:34px;background:var(--acc);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.bmark svg{width:18px;height:18px;fill:#fff}
.brand h1{font-size:13px;font-weight:700;color:#fff}
.brand p{font-size:11px;color:var(--tx3);margin-top:2px}
.srch{padding:10px 10px 6px;flex-shrink:0}
.srch input{width:100%;background:#1f2235;border:1px solid #2a2f50;border-radius:7px;padding:7px 11px;font-size:12px;color:#ccc;outline:none}
.srch input::placeholder{color:#555}
.srch input:focus{border-color:var(--acc)}
nav{flex:1;overflow-y:auto;padding:4px 8px 16px}
nav::-webkit-scrollbar{width:3px}
nav::-webkit-scrollbar-thumb{background:#2a2f50;border-radius:2px}
.ng{font-size:10px;font-weight:700;letter-spacing:.07em;color:#555;padding:12px 8px 5px;text-transform:uppercase}
.ni{display:flex;align-items:center;gap:7px;padding:7px 9px;border-radius:7px;cursor:pointer;transition:.12s;margin-bottom:1px}
.ni:hover{background:#1f2235}
.ni.on{background:var(--acc)}
.ni-dot{width:5px;height:5px;border-radius:50%;background:#444;flex-shrink:0}
.ni.on .ni-dot{background:rgba(255,255,255,.6)}
.ni-lbl{font-size:12px;color:#bbb;flex:1}
.ni.on .ni-lbl{color:#fff;font-weight:500}
.ni-bg{font-size:10px;padding:2px 6px;border-radius:4px;font-weight:600}
.b1{background:rgba(124,111,205,.2);color:#a99ee8}
.b2{background:rgba(15,110,86,.2);color:#5dcaa5}
.b3{background:rgba(153,60,29,.2);color:#f0a080}
.bc{background:rgba(80,80,120,.2);color:#9090c0}
.bm{background:rgba(186,117,23,.2);color:#e8b050}

.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0}
.top{background:var(--card);border-bottom:1px solid var(--bd);padding:0 26px;height:50px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.top-t{font-size:14px;font-weight:600}
.top-s{font-size:11px;color:var(--tx2)}
.sdot{width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;margin-right:5px}
.stxt{font-size:12px;color:var(--tx2)}

.cnt{flex:1;overflow-y:auto;padding:22px 26px}
.cnt::-webkit-scrollbar{width:5px}
.cnt::-webkit-scrollbar-thumb{background:var(--bd);border-radius:3px}

.ld{display:flex;align-items:center;justify-content:center;height:220px;gap:10px;color:var(--tx2);font-size:14px}
.sp{width:20px;height:20px;border:2px solid var(--bd);border-top-color:var(--acc);border-radius:50%;animation:sp .7s linear infinite}
@keyframes sp{to{transform:rotate(360deg)}}

.ph{margin-bottom:18px}
.ph h2{font-size:19px;font-weight:700;margin-bottom:4px}
.ph p{font-size:13px;color:var(--tx2);line-height:1.5}
.pill{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;margin-bottom:9px}
.pp1{background:rgba(124,111,205,.1);color:#5047a0;border:1px solid rgba(124,111,205,.2)}
.pp2{background:rgba(15,110,86,.1);color:#0a5038;border:1px solid rgba(15,110,86,.2)}
.pp3{background:rgba(153,60,29,.1);color:#7a2810;border:1px solid rgba(153,60,29,.2)}
.ppc{background:rgba(80,80,130,.08);color:#383868;border:1px solid rgba(80,80,130,.15)}
.ppm{background:rgba(186,117,23,.1);color:#7a4d10;border:1px solid rgba(186,117,23,.2)}

.r2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.r1{margin-bottom:14px}
.card{background:var(--card);border-radius:var(--r);border:1px solid var(--bd);padding:16px 18px}
.ct{font-size:12px;font-weight:600;color:var(--tx2);margin-bottom:12px;display:flex;align-items:center;gap:6px}
.ci{width:18px;height:18px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}

.sql-wrap{margin-bottom:14px}
.sql-toggle{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#0d1117;border-radius:8px;cursor:pointer;user-select:none;border:1px solid #21262d}
.sql-toggle:hover{background:#161b22}
.sql-arrow{font-size:11px;color:#7c6fcd;transition:transform .2s;flex-shrink:0}
.sql-arrow.open{transform:rotate(90deg)}
.sql-label{font-size:11px;font-weight:600;color:#8b949e;flex:1}
.sql-body{display:none;background:#0d1117;border:1px solid #21262d;border-top:none;border-radius:0 0 8px 8px;padding:14px 16px}
.sql-body.open{display:block}
.sql-body pre{font-family:'Courier New',monospace;font-size:12px;line-height:1.8;white-space:pre-wrap;word-break:break-word;color:#e6edf3;margin:0}
.k{color:#ff7b72;font-weight:600}
.s{color:#a5d6ff}
.n{color:#f2cc60}
.f{color:#7ee787}

.tw{overflow-x:auto;border-radius:8px;border:1px solid var(--bd)}
table{width:100%;border-collapse:collapse;font-size:12.5px}
th{background:#f5f6fc;padding:8px 11px;text-align:left;font-weight:600;color:var(--tx2);font-size:11px;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid var(--bd);white-space:nowrap;cursor:pointer;user-select:none}
th:hover{background:#eef0fa;color:var(--acc)}
th.sa::after{content:' ↑';color:var(--acc)}
th.sd::after{content:' ↓';color:var(--acc)}
td{padding:7px 11px;border-bottom:1px solid #f0f1f8;color:var(--tx);vertical-align:middle;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f8f9ff}
.tpub{background:#dcfce7;color:#166534;font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700}
.tdft{background:#fef9c3;color:#854d0e;font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700}
.tarh{background:#f1f5f9;color:#475569;font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700}

.kbox{background:rgba(124,111,205,.07);border:1px solid rgba(124,111,205,.2);border-radius:8px;padding:12px 15px;margin-top:12px}
.klbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--acc);margin-bottom:5px}
.kbox p{font-size:12.5px;color:var(--tx2);line-height:1.6}
.kbox strong{color:var(--acc)}

.kpi-g{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px}
.kpi-c{background:var(--card);border:1px solid var(--bd);border-radius:var(--r);padding:14px 16px}
.kpi-c.ac{border-top:3px solid var(--acc)}
.kpi-v{font-size:26px;font-weight:700;color:var(--tx)}
.kpi-l{font-size:11px;color:var(--tx2);margin-top:4px}
.kpi-s{font-size:10px;color:var(--tx3);margin-top:2px}

.rf{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
.rb{padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;border:1.5px solid var(--bd);background:var(--card);color:var(--tx2);transition:.12s}
.rb:hover,.rb.on{background:var(--acc);color:#fff;border-color:var(--acc)}
canvas{max-height:320px!important}
.card-chart{min-height:340px;display:flex;flex-direction:column}
.card-chart canvas{flex:1;min-height:300px!important}

.em{text-align:center;padding:30px;color:var(--tx3);font-size:13px}
.err{color:#a32d2d;padding:10px;background:#fcebeb;border-radius:8px;font-size:13px}
.hidden{display:none!important}
.user-card{padding:12px 14px;border-top:1px solid #1f2235;flex-shrink:0;display:flex;align-items:center;gap:10px}
.u-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.u-adm{background:#534AB7;color:#fff}
.u-red{background:#0F6E56;color:#fff}
.u-cit{background:#7A2F15;color:#fff}
.u-name{font-size:12px;font-weight:600;color:#ccd;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u-rol{font-size:10px;color:#666;margin-top:1px}
.logout-btn{background:none;border:none;cursor:pointer;color:#555;font-size:11px;padding:4px 6px;border-radius:5px;transition:.15s;text-decoration:none;display:block}
.logout-btn:hover{background:#2a1520;color:#f09595}
.role-banner{padding:6px 14px;font-size:11px;font-weight:600;text-align:center;flex-shrink:0}
.rb-adm{background:rgba(83,74,183,.25);color:#a89de8;border-bottom:1px solid rgba(83,74,183,.3)}
.rb-red{background:rgba(15,110,86,.2);color:#5dcaa5;border-bottom:1px solid rgba(15,110,86,.25)}
.rb-cit{background:rgba(122,47,21,.2);color:#f0a080;border-bottom:1px solid rgba(122,47,21,.25)}
.abtn{background:var(--acc);color:#fff;border:none;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s}
.abtn:hover{background:#6a5ec0}
.cbtn{background:var(--card);color:var(--tx2);border:1px solid var(--bd);border-radius:7px;padding:7px 14px;font-size:12px;cursor:pointer}
.cbtn:hover{background:var(--bg)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:16px;background:var(--bg);border-radius:8px;border:1px solid var(--bd);margin-bottom:4px}
.fg{display:flex;flex-direction:column;gap:4px}
.fg-full{grid-column:1/-1}
.fg label{font-size:11px;font-weight:600;color:var(--tx2);text-transform:uppercase;letter-spacing:.04em}
.fg input,.fg select,.fg textarea{background:var(--card);border:1px solid var(--bd);border-radius:6px;padding:7px 10px;font-size:13px;color:var(--tx);font-family:Arial,sans-serif;outline:none;transition:.15s;width:100%}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--acc)}
.fg textarea{resize:vertical;min-height:80px}
</style>
</head>
<body>

<aside class="sb">
  <div class="brand">
    <div class="bmark">
      <svg viewBox="0 0 18 18"><rect x="1" y="1" width="7" height="7" rx="1.5"/><rect x="10" y="1" width="7" height="7" rx="1.5"/><rect x="1" y="10" width="7" height="7" rx="1.5"/><rect x="10" y="10" width="7" height="4" rx="1.5"/><rect x="13" y="16" width="4" height="1.5" rx=".75"/></svg>
    </div>
    <h1>Portal Editorial</h1>
  </div>
  <div class="role-banner rb-<?= substr($rol,0,3) ?>">
    Conectat ca: <?= ucfirst($rol) ?>
  </div>
  <div class="srch"><input type="text" id="srch" placeholder="Caută obiectiv..."></div>
  <nav id="nav"></nav>
  <div class="user-card">
    <div class="u-avatar u-<?= substr($rol,0,3) ?>"><?= strtoupper(substr($user['nume'],0,2)) ?></div>
    <div style="flex:1;min-width:0">
      <div class="u-name"><?= htmlspecialchars($user['nume']) ?></div>
      <div class="u-rol"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <a href="logout.php" class="logout-btn" title="Deconectare">⏻</a>
    <a href="crud.php" class="logout-btn" title="Modul CRUD" style="color:#7c6fcd">⚙</a>
  </div>
</aside>

<div class="main">
  <div class="top">
    <div>
      <div class="top-t" id="tt">Dashboard</div>
    </div>
  </div>
  <div class="cnt" id="cnt">
    <div class="ld"><div class="sp"></div> Se încarcă...</div>
  </div>
</div>

<script>
const ROL = '<?= $rol ?>';
const POATE = <?= json_encode($_SESSION['permisiuni'] ?? []) ?>;

const MENU_FULL = [
  {id:'dashboard',lbl:'Dashboard',bg:'bc'},
  {g:'DB1 — Utilizatori'},
  {id:'1', lbl:'O1 · Lista utilizatorilor',    bg:'b1', roles:['administrator']},
  {id:'2', lbl:'O2 · Distribuție roluri',       bg:'b1', roles:['administrator']},
  {id:'3', lbl:'O3 · Utilizatori inactivi',     bg:'b1', roles:['administrator']},
  {g:'DB2 — Conținut'},
  {id:'4', lbl:'O4 · Articole publicate',       bg:'b2', roles:['administrator','redactor','cititor']},
  {id:'5', lbl:'O5 · Articole per categorie',   bg:'b2', roles:['administrator','redactor']},
  {id:'6', lbl:'O6 · Top 3 vizualizări',        bg:'b2', roles:['administrator','redactor','cititor']},
  {id:'7', lbl:'O7 · Articole draft',           bg:'b2', roles:['administrator','redactor']},
  {g:'DB3 — Interacțiuni'},
  {id:'8', lbl:'O8 · Media evaluărilor',        bg:'b3', roles:['administrator','redactor','cititor']},
  {id:'9', lbl:'O9 · Fire de discuție',         bg:'b3', roles:['administrator','redactor','cititor']},
  {id:'10',lbl:'O10 · Vizualizări 7 zile',      bg:'b3', roles:['administrator','redactor']},
  {g:'Cross-Node'},
  {id:'11',lbl:'O11 · Clasament autori',        bg:'bc', roles:['administrator']},
  {id:'12',lbl:'O12 · Articole populare',       bg:'bc', roles:['administrator','redactor']},
  {id:'13',lbl:'O13 · Cititori activi',         bg:'bc', roles:['administrator']},
  {id:'14',lbl:'O14 · KPI Dashboard',           bg:'bc', roles:['administrator']},
  {g:'Cerințe speciale'},
  {id:'rol',     lbl:'Acces diferențiat pe rol',   bg:'bc', roles:['administrator']},
  {id:'metadata',lbl:'Structura BD — metadate',    bg:'bm', roles:['administrator']},
  {id:'json',    lbl:'Schema noduri ca JSON',       bg:'bm', roles:['administrator']},
  {id:'subquery',lbl:'Articole peste media notelor',bg:'bc', roles:['administrator','redactor']},
];

const MENU = MENU_FULL.filter(m => m.g || !m.roles || m.roles.includes(ROL));

let cur = '', curRol = '', charts = [];

function buildNav(f) {
  f = (f||'').toLowerCase();
  document.getElementById('nav').innerHTML = MENU.map(m => {
    if (m.g) return `<div class="ng">${m.g}</div>`;
    if (f && !m.lbl.toLowerCase().includes(f)) return '';
    const on = m.id === cur ? 'on' : '';
    return `<div class="ni ${on}" data-id="${m.id}"><div class="ni-dot"></div><span class="ni-lbl">${m.lbl}</span><span class="ni-bg ${m.bg}">${m.bg==='b1'?'DB1':m.bg==='b2'?'DB2':m.bg==='b3'?'DB3':m.bg==='bm'?'META':'×'}</span></div>`;
  }).join('');
  document.querySelectorAll('.ni[data-id]').forEach(el =>
    el.addEventListener('click', () => loadPage(el.dataset.id)));
}

function pill(nod) {
  const cls = nod.includes('×') || nod.includes('Cross') ? 'ppc'
    : nod.includes('INFO') ? 'ppm'
    : nod.includes('DB1') ? 'pp1'
    : nod.includes('DB2') ? 'pp2'
    : nod.includes('DB3') ? 'pp3' : 'ppc';
  return `<span class="pill ${cls}">● ${nod}</span>`;
}

function toggleSql(el) {
  const body  = el.nextElementSibling;
  const arrow = el.querySelector('.sql-arrow');
  const hint  = el.querySelector('span:last-child');
  const open  = body.classList.toggle('open');
  arrow.classList.toggle('open', open);
  el.querySelector('.sql-arrow').textContent = open ? '▼' : '▶';
  if(hint) hint.textContent = open ? '' : 'click pentru a vedea';
}

function hlSql(s) {
  s = s.trim().replace(/;?\s*$/, ';');
  // Pune fiecare keyword principal pe linie noua
  const kw = ['SELECT','FROM','JOIN','LEFT JOIN','RIGHT JOIN','INNER JOIN','WHERE','GROUP BY','HAVING','ORDER BY','LIMIT','ON','AND','OR','UNION','INSERT INTO','UPDATE','SET','DELETE FROM','VALUES'];
  // Normalizeaza spatiile
  s = s.replace(/\s+/g, ' ');
  kw.forEach(k => {
    const re = new RegExp('\\b' + k + '\\b', 'g');
    s = s.replace(re, '\n' + k);
  });
  s = s.trim();
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function tbl(cols, rows) {
  if (!rows || !rows.length) return `<div class="em">Niciun rezultat returnat.</div>`;
  const ths = cols.map((c,i)=>`<th data-i="${i}">${c}</th>`).join('');
  const trs = rows.map(row => '<tr>'+cols.map(c=>{
    let v = row[c]??'';
    if (c==='status') v = v?`<span class="t${v.substring(0,1)+'ub'.includes(v[1])?'pub':'dft'}">${v}</span>`:v;
    return `<td title="${String(v).replace(/"/g,'&quot;')}">${v}</td>`;
  }).join('')+'</tr>').join('');
  return `<div class="tw"><table><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table></div>`;
}

function mkChart(cfg, cid) {
  charts.forEach(c => { try{c.destroy();}catch(e){} });
  charts = [];
  const cv = document.getElementById(cid);
  if (!cv || !cfg || cfg.tip==='kpi') return;
  const ds = cfg.seturi
    ? cfg.seturi.map(s=>({label:s.label,data:s.valori,backgroundColor:s.culoare+'BB',borderColor:s.culoare,borderWidth:1,borderRadius:4}))
    : [{data:cfg.valori,backgroundColor:cfg.tip==='pie'?cfg.culori:cfg.culoare+'CC',borderColor:cfg.tip==='pie'?'#fff':cfg.culoare,borderWidth:cfg.tip==='pie'?2:1,borderRadius:4}];
  const ch = new Chart(cv, {
    type: cfg.tip==='line'?'line':cfg.tip==='pie'?'pie':'bar',
    data: {labels:cfg.etichete, datasets:ds},
    options:{
      responsive:true, maintainAspectRatio:false,
      plugins:{legend:{position:cfg.tip==='pie'?'right':'bottom',labels:{font:{size:11},padding:10,boxWidth:12}}},
      ...(cfg.tip!=='pie'?{scales:{y:{beginAtZero:true,grid:{color:'#f0f1f8'},ticks:{font:{size:13}}},x:{ticks:{font:{size:12}}}}}:{}),
      ...(cfg.tip==='line'?{elements:{line:{tension:0.3,fill:true},point:{radius:4}}}:{}),
    }
  });
  charts.push(ch);
}

function kpiCards(v) {
  // suporta ambele formate de chei (dashboard si O14)
  function gv(k1, k2){ return v[k1] ?? v[k2] ?? '—'; }
  const it = [
    {v:gv('utilizatori','total_utilizatori'),    l:'Utilizatori',       s:'activi în sistem',   ac:true},
    {v:gv('publicate','articole_publicate'),      l:'Articole publicate',s:'disponibile public'},
    {v:gv('draft','articole_draft'),              l:'Articole draft',    s:'necesită publicare'},
    {v:gv('comentarii','total_comentarii'),       l:'Comentarii',        s:'aprobate'},
    {v:gv('medie_nota','medie_evaluari'),         l:'Medie evaluări',    s:'din 5 stele',        ac:true},
    {v:gv('vizualizari','total_vizualizari'),     l:'Vizualizări',       s:'înregistrate'},
  ];
  return `<div class="kpi-g">${it.map(i=>`<div class="kpi-c${i.ac?' ac':''}"><div class="kpi-v">${i.v}</div><div class="kpi-l">${i.l}</div><div class="kpi-s">${i.s}</div></div>`).join('')}</div>`;
}

function sortTbl(th, col) {
  const table = th.closest('table');
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const asc = th.classList.contains('sd') || !th.classList.contains('sa');
  table.querySelectorAll('th').forEach(h=>h.classList.remove('sa','sd'));
  th.classList.add(asc?'sa':'sd');
  rows.sort((a,b)=>{
    const va=a.cells[col]?.textContent.trim()||'', vb=b.cells[col]?.textContent.trim()||'';
    const na=parseFloat(va), nb=parseFloat(vb);
    if(!isNaN(na)&&!isNaN(nb)) return asc?na-nb:nb-na;
    return asc?va.localeCompare(vb,'ro'):vb.localeCompare(va,'ro');
  });
  rows.forEach(r=>tbody.appendChild(r));
}

async function loadPage(id, extra) {
  cur = id;
  buildNav(document.getElementById('srch').value);
  const c = document.getElementById('cnt');
  c.innerHTML = `<div class="ld"><div class="sp"></div> Se încarcă...</div>`;
  const item = MENU.find(m=>m.id===id);
  document.getElementById('tt').textContent = (item?.lbl||id).replace(/O\d+ · /,'');

  try {
    const url = 'api/data.php?ob='+id+(extra||'');
    const res = await fetch(url);
    if (!res.ok) { c.innerHTML=`<div class="err">HTTP ${res.status}</div>`; return; }
    const d = await res.json();
    if (d.eroare) { c.innerHTML=`<div class="err">Eroare BD: ${d.eroare}</div>`; return; }

    if (d.tip === 'dashboard') {
      c.innerHTML = kpiCards(d.kpi) + `
        <div class="r2">
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#eeedfe;color:#7c6fcd">R</div>Distribuție utilizatori pe roluri</div><canvas id="c1" height="280"></canvas></div>
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#f0fdf4;color:#0F6E56">■</div>Status articole</div><canvas id="c6" height="280"></canvas></div>
        </div>
        <div class="r2">
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#e1f5ee;color:#0F6E56">C</div>Articole per categorie</div><canvas id="c2" height="280"></canvas></div>
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#eeedfe;color:#7c6fcd">↑</div>Top articole după vizualizări</div><canvas id="c3" height="280"></canvas></div>
        </div>
        <div class="r2">
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#faece7;color:#993C1D">★</div>Media evaluărilor per articol</div><canvas id="c4" height="280"></canvas></div>
          <div class="card card-chart"><div class="ct"><div class="ci" style="background:#faeeda;color:#BA7517">~</div>Trend vizualizări zilnice</div><canvas id="c5" height="280"></canvas></div>
        </div>`;
      setTimeout(() => {
        mkChart(d.chart_roluri, 'c1');
        mkChart(d.chart_status, 'c6');
        const mkBar = (cfg, id) => {
          const cv = document.getElementById(id);
          if(!cv || !cfg) return;
          const ch = new Chart(cv, {type:'bar',data:{labels:cfg.etichete,datasets:[{data:cfg.valori,backgroundColor:cfg.culoare+'CC',borderColor:cfg.culoare,borderWidth:1,borderRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{font:{size:12}}},x:{ticks:{font:{size:11},maxRotation:35}}}}});
          charts.push(ch);
        };
        mkBar(d.chart_categorii, 'c2');
        mkBar(d.chart_top, 'c3');
        mkBar(d.chart_eval, 'c4');
        if(d.chart_stat){
          const cv5 = document.getElementById('c5');
          if(cv5){
            const ch5 = new Chart(cv5,{type:'line',data:{labels:d.chart_stat.etichete,datasets:[{data:d.chart_stat.valori,borderColor:d.chart_stat.culoare,backgroundColor:d.chart_stat.culoare+'22',fill:true,tension:0.3,pointRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{font:{size:12}}},x:{ticks:{font:{size:11}}}}}});
            charts.push(ch5);
          }
        }
      }, 60);
      return;
    }

    const hasChart = d.chart && d.chart.tip !== 'kpi';
    const isKpi    = d.chart && d.chart.tip === 'kpi';
    const isRol    = id === 'rol';

    c.innerHTML = `
      <div class="ph">
        ${pill(d.nod||'')}
        <h2>${d.titlu||''}</h2>
        <p>${d.descriere||''}</p>
      </div>
      ${isRol ? `<div class="rf" id="rf">
        ${['','administrator','redactor','cititor'].map(r=>`<button class="rb${curRol===r?' on':''}" onclick="loadRol('${r}')">${r||'Toate rolurile'}</button>`).join('')}
      </div>` : ''}
      ${isKpi ? kpiCards(d.chart.valori) : ''}
      <div class="r1 sql-wrap">
        <div class="sql-toggle" onclick="toggleSql(this)">
          <span class="sql-arrow">▶</span>
          <span class="sql-label">&lt;/&gt; &nbsp;Interogare SQL</span>
          <span style="font-size:10px;color:#555">click pentru a vedea</span>
        </div>
        <div class="sql-body"><pre>${hlSql(d.sql||'')}</pre></div>
      </div>
      ${hasChart?`<div class="card r1 card-chart"><div class="ct"><div class="ci" style="background:#eeedfe;color:#7c6fcd">↗</div>Grafic</div><canvas id="mc" height="320"></canvas></div>`:''}
      <div class="card r1">
        <div class="ct"><div class="ci" style="background:#f0f2f8;color:#5a5a7a">≡</div>Rezultate
          <span class="bc2">${(d.date||[]).length} rânduri</span></div>
        ${tbl(d.coloane||[], d.date||[])}
      </div>
      ${d.kpi?`<div class="kbox"><div class="klbl">Impact KPI</div><p><strong>→</strong> ${d.kpi}</p></div>`:''}`;

    if (hasChart) setTimeout(() => mkChart(d.chart, 'mc'), 60);
    c.querySelectorAll('th').forEach((th,i) => th.addEventListener('click', ()=>sortTbl(th,i)));

  } catch(e) {
    c.innerHTML = `<div class="err">Eroare: ${e.message}</div>`;
  }
}

function loadRol(r) {
  curRol = r;
  loadPage('rol', r ? '&rol='+r : '');
}

async function submitArticol() {
  const msg = document.getElementById('form-msg');
  const titlu    = document.getElementById('f-titlu')?.value.trim();
  const continut = document.getElementById('f-cont')?.value.trim();
  const id_cat   = document.getElementById('f-cat')?.value;
  const rezumat  = document.getElementById('f-rez')?.value.trim();
  const status   = document.getElementById('f-status')?.value;

  if (!titlu || !continut || !id_cat) {
    msg.innerHTML = '<span style="color:#a32d2d">Completează câmpurile obligatorii: Titlu, Conținut, Categorie.</span>';
    return;
  }

  const fd = new FormData();
  fd.append('titlu',       titlu);
  fd.append('continut',    continut);
  fd.append('rezumat',     rezumat);
  fd.append('id_autor',    1);
  fd.append('id_categorie',id_cat);
  fd.append('status',      status);

  msg.innerHTML = '<span style="color:var(--acc)">Se salvează...</span>';
  try {
    const r = await fetch('api/data.php?ob=adauga_articol', {method:'POST', body:fd});
    const d = await r.json();
    if (d.ok) {
      msg.innerHTML = `<span style="color:#0F6E56">✓ ${d.mesaj} (ID: ${d.id})</span>`;
      document.getElementById('f-titlu').value='';
      document.getElementById('f-cont').value='';
      document.getElementById('f-rez').value='';
      setTimeout(() => loadPage('dashboard'), 1200);
    } else {
      msg.innerHTML = `<span style="color:#a32d2d">Eroare: ${d.eroare}</span>`;
    }
  } catch(e) {
    msg.innerHTML = `<span style="color:#a32d2d">Eroare rețea: ${e.message}</span>`;
  }
}

document.getElementById('srch').addEventListener('input', e => buildNav(e.target.value));
buildNav('');
loadPage('dashboard');
</script>
</body>
</html>
