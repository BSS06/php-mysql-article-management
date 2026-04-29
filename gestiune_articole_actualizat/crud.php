<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/auth.php';
necesita_autentificare();
$rol = rol_curent();
$user = utilizator_curent();
if(!in_array($rol, array('administrator','redactor'))){
    header('Location: index.php'); exit;
}
$is_admin = ($rol === 'administrator');
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CRUD — Portal Editorial</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#f0f2f8;color:#1a1a2e}
.hdr{background:#12151f;border-bottom:3px solid #7c6fcd;padding:12px 24px;display:flex;align-items:center;justify-content:space-between}
.hdr h1{font-size:15px;font-weight:700;color:#fff}
.hdr-r{display:flex;align-items:center;gap:10px}
.rb{font-size:11px;padding:3px 10px;border-radius:99px;font-weight:700}
.rb-administrator{background:#534AB7;color:#fff}
.rb-redactor{background:#0F6E56;color:#fff}
.hbtn{font-size:12px;color:#9090a8;text-decoration:none;padding:5px 10px;border-radius:6px;border:1px solid #2a2f50}
.hbtn:hover{background:#1f2235;color:#fff}
.banner{padding:8px 24px;font-size:12px;<?= $is_admin?'background:#2a2550;color:#a89de8;border-bottom:1px solid #3a3570':'background:#0a2e20;color:#5dcaa5;border-bottom:1px solid #0f4030' ?>}
.tabs{background:#fff;border-bottom:1px solid #e4e6f0;padding:0 24px;display:flex;gap:0;overflow-x:auto}
.tab{padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;border-bottom:3px solid transparent;color:#6b6b7a;white-space:nowrap}
.tab:hover{color:#7c6fcd}
.tab.on{color:#7c6fcd;border-bottom-color:#7c6fcd}
.pg{padding:20px 24px;max-width:1300px}
.pnl{display:none}
.pnl.on{display:block}
.tbr{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.tbr h2{font-size:15px;font-weight:700}
.btn{padding:7px 15px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:Arial,sans-serif}
.pri{background:#7c6fcd;color:#fff}.pri:hover{background:#6a5ec0}
.suc{background:#0F6E56;color:#fff}.suc:hover{background:#085041}
.dan{background:#993C1D;color:#fff}.dan:hover{background:#7a2f15}
.sec{background:#f0f2f8;color:#5a5a7a;border:1px solid #e4e6f0}.sec:hover{background:#e4e6f0}
.wrn{background:#BA7517;color:#fff}.wrn:hover{background:#8a5510}
.sm{padding:4px 10px;font-size:11px}
.tw{overflow-x:auto;border-radius:8px;border:1px solid #e4e6f0;background:#fff;margin-bottom:14px}
table{width:100%;border-collapse:collapse;font-size:12.5px}
th{background:#f5f6fc;padding:8px 11px;text-align:left;font-weight:600;color:#6b6b7a;font-size:11px;text-transform:uppercase;border-bottom:1px solid #e4e6f0;white-space:nowrap}
td{padding:7px 11px;border-bottom:1px solid #f0f1f8;vertical-align:middle;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
tr:last-child td{border:none}
tr:hover td{background:#f8f9ff}
.act{display:flex;gap:4px}
.bdg{font-size:10px;padding:2px 7px;border-radius:4px;font-weight:700}
.pub{background:#dcfce7;color:#166534}.dra{background:#fef9c3;color:#854d0e}.arh{background:#f1f5f9;color:#475569}
.bok{background:#E1F5EE;color:#085041}.bno{background:#FAECE7;color:#712B13}
.b1{background:#EEEDFE;color:#3C3489}
.jc{font-family:monospace;font-size:10px;color:#534AB7;background:#f5f4ff;padding:2px 5px;border-radius:4px}
.msg{padding:9px 13px;border-radius:7px;font-size:12px;margin-bottom:12px;display:none}
.msg.on{display:block}
.mok{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
.mer{background:#fce8e8;color:#991b1b;border:1px solid #fca5a5}
.mb{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;align-items:center;justify-content:center}
.mb.on{display:flex}
.md{background:#fff;border-radius:12px;padding:22px 24px;width:100%;max-width:460px;max-height:90vh;overflow-y:auto}
.md h3{font-size:15px;font-weight:700;margin-bottom:16px}
.fg{margin-bottom:11px}
.fg label{display:block;font-size:11px;font-weight:700;color:#6b6b7a;text-transform:uppercase;margin-bottom:4px}
.fg input,.fg select,.fg textarea{width:100%;padding:8px 10px;border:1px solid #e4e6f0;border-radius:7px;font-size:13px;font-family:Arial,sans-serif;outline:none;color:#1a1a2e}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#7c6fcd}
.fg textarea{resize:vertical;min-height:80px}
.mact{display:flex;gap:8px;margin-top:14px}
.ld{color:#7c6fcd;font-size:13px;padding:12px 0}
.ir{padding:11px 13px;border-radius:8px;margin-bottom:7px;display:flex;gap:10px}
.ir-pass{background:#f0fdf4;border:1px solid #bbf7d0}
.ir-info{background:#eff6ff;border:1px solid #bfdbfe}
.ir-warn{background:#fffbeb;border:1px solid #fde68a}
.ir-fail{background:#fef2f2;border:1px solid #fca5a5}
.is{font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;white-space:nowrap;margin-top:2px;flex-shrink:0}
.sp{background:#dcfce7;color:#166534}.si{background:#dbeafe;color:#1e40af}.sw{background:#fef9c3;color:#854d0e}.sf{background:#fee2e2;color:#991b1b}
.ib{flex:1}
.it{font-size:13px;font-weight:600;margin-bottom:2px}
.ia{font-size:11px;color:#6b6b7a;font-family:monospace;margin-bottom:2px}
.iz{font-size:12px;color:#374151}
.sm2{display:flex;gap:10px;padding:12px;background:#f5f6fc;border-radius:8px;margin-bottom:14px;font-size:13px}
</style>
</head>
<body>
<div class="hdr">
  <h1>Portal Editorial — CRUD</h1>
  <div class="hdr-r">
    <span class="rb rb-<?=$rol?>"><?=ucfirst($rol)?></span>
    <span style="font-size:12px;color:#9090a8"><?=htmlspecialchars($user['nume'])?></span>
    <a href="index.php" class="hbtn">← Dashboard</a>
    <a href="logout.php" class="hbtn">Ieșire</a>
  </div>
</div>
<div class="banner">
  <?=$is_admin?'Administrator — CREATE/READ/UPDATE/DELETE complet pe toate tabelele și nodurile.':'Redactor — Gestionezi articolele proprii (CRUD). Comentariile cititorilor sunt vizibile read-only.'?>
</div>
<div class="tabs">
  <?php if($is_admin):?>
  <div class="tab on" onclick="T(this,'utilizatori')">Utilizatori</div>
  <div class="tab" onclick="T(this,'roluri')">Roluri</div>
  <?php endif;?>
  <div class="tab<?=$is_admin?'':' on'?>" onclick="T(this,'articole')">Articole<?=$is_admin?'':' (ale mele)'?></div>
  <?php if($is_admin):?>
  <div class="tab" onclick="T(this,'categorii')">Categorii</div>
  <?php endif;?>
  <div class="tab" onclick="T(this,'comentarii')">Comentarii</div>
  <?php if($is_admin):?><div class="tab" onclick="T(this,'evaluari')">Evaluări</div><?php endif;?>
  <?php if($is_admin):?>
  <div class="tab" onclick="T(this,'integritate')">Testare Integritate</div>
  <?php endif;?>
</div>

<div class="pg">
<div id="M" class="msg"></div>

<?php if($is_admin):?>
<div id="pnl-utilizatori" class="pnl on">
  <div class="tbr"><h2>Utilizatori — DB1</h2><button class="btn pri" onclick="OM('m-au')">+ Utilizator nou</button></div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Nume</th><th>Email</th><th>Rol</th><th>Activ</th><th>Înregistrat</th><th>JSON</th><th>Acțiuni</th></tr></thead><tbody id="b-utilizatori"><tr><td colspan="8" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div>
<div id="pnl-roluri" class="pnl">
  <div class="tbr"><h2>Roluri — DB1 (read only)</h2></div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Rol</th><th>Descriere</th><th>Utilizatori</th><th>JSON</th></tr></thead><tbody id="b-roluri"><tr><td colspan="5" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div>
<?php endif;?>

<div id="pnl-articole" class="pnl<?=$is_admin?'':' on'?>">
  <div class="tbr"><h2>Articole — DB2<?=$is_admin?'':' (ale mele)'?></h2><button class="btn pri" onclick="OM('m-aa')">+ Articol nou</button></div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Titlu</th><th>Autor</th><th>Categorie</th><th>Status</th><th>Vizualizări</th><th>JSON</th><th>Acțiuni</th></tr></thead><tbody id="b-articole"><tr><td colspan="8" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div>

<?php if($is_admin):?>
<div id="pnl-categorii" class="pnl">
  <div class="tbr"><h2>Categorii — DB2</h2><button class="btn pri" onclick="OM('m-ac')">+ Categorie nouă</button></div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Categorie</th><th>Descriere</th><th>Articole</th><th>JSON</th><th>Acțiuni</th></tr></thead><tbody id="b-categorii"><tr><td colspan="6" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div>
<?php endif;?>

<div id="pnl-comentarii" class="pnl">
  <div class="tbr">
    <h2>Comentarii — DB3<?=$is_admin?'':' (vizualizare feedback)'?></h2>
    <?php if($is_admin):?>
    <button class="btn pri" onclick="OM('m-acm')">+ Comentariu nou</button>
    <?php else:?>
    <span style="font-size:12px;color:#6b6b7a;font-style:italic">Comentariile sunt feedback al cititorilor — vizualizare read-only</span>
    <?php endif;?>
  </div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Articol</th><th>Autor</th><th>Text</th><th>Aprobat</th><th>Data</th><th>JSON</th><?=$is_admin?'<th>Acțiuni</th>':''?></tr></thead><tbody id="b-comentarii"><tr><td colspan="<?=$is_admin?8:7?>" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div>

<?php if($is_admin):?><div id="pnl-evaluari" class="pnl">
  <div class="tbr"><h2>Evaluări — DB3</h2><button class="btn pri" onclick="OM('m-aev')">+ Evaluare nouă</button></div>
  <div class="tw"><table><thead><tr><th>ID</th><th>Articol</th><th>Autor</th><th>Notă</th><th>Data</th><th>JSON</th><th>Acțiuni</th></tr></thead><tbody id="b-evaluari"><tr><td colspan="7" class="ld">Se încarcă...</td></tr></tbody></table></div>
</div><?php endif;?>

<?php if($is_admin):?>
<div id="pnl-integritate" class="pnl">
  <div class="tbr"><h2>Testare Integritate Multi-User</h2><button class="btn wrn" onclick="testInt()">▶ Rulează testele</button></div>
  <p style="font-size:13px;color:#6b6b7a;margin-bottom:14px">Simulare: UNIQUE constraints, FK logic cross-node, CHECK nota, CASCADE DELETE, concurență atomică.</p>
  <div id="int-r"></div>
</div>
<?php endif;?>
</div>

<!-- MODALS -->
<div id="m-au" class="mb"><div class="md"><h3>Utilizator nou</h3>
  <div class="fg"><label>Prenume *</label><input id="u-pr" type="text"></div>
  <div class="fg"><label>Nume *</label><input id="u-nu" type="text"></div>
  <div class="fg"><label>Email *</label><input id="u-em" type="email"></div>
  <div class="fg"><label>Parolă * (min 4)</label><input id="u-pa" type="password"></div>
  <div class="fg"><label>Rol</label><select id="u-ro"><option value="1">Administrator</option><option value="2">Redactor</option><option value="3" selected>Cititor</option></select></div>
  <div class="mact"><button class="btn suc" onclick="crU()">Salvează</button><button class="btn sec" onclick="CM('m-au')">Anulează</button></div>
</div></div>

<div id="m-eu" class="mb"><div class="md"><h3>Editează utilizator</h3>
  <input type="hidden" id="eu-id">
  <div class="fg"><label>Prenume *</label><input id="eu-pr" type="text"></div>
  <div class="fg"><label>Nume *</label><input id="eu-nu" type="text"></div>
  <div class="fg"><label>Email *</label><input id="eu-em" type="email"></div>
  <div class="fg"><label>Rol</label><select id="eu-ro"><option value="1">Administrator</option><option value="2">Redactor</option><option value="3">Cititor</option></select></div>
  <div class="fg"><label>Activ</label><select id="eu-ac"><option value="1">Da</option><option value="0">Nu</option></select></div>
  <div class="mact"><button class="btn suc" onclick="upU()">Salvează</button><button class="btn sec" onclick="CM('m-eu')">Anulează</button></div>
</div></div>

<div id="m-aa" class="mb"><div class="md"><h3>Articol nou</h3>
  <div class="fg"><label>Titlu * (min 5)</label><input id="a-ti" type="text"></div>
  <div class="fg"><label>Rezumat</label><input id="a-re" type="text"></div>
  <div class="fg"><label>Conținut * (min 10)</label><textarea id="a-co" rows="4"></textarea></div>
  <div class="fg"><label>Categorie *</label><select id="a-ca"></select></div>
  <div class="fg"><label>Status</label><select id="a-st"><option value="draft">Draft</option><option value="publicat">Publicat</option></select></div>
  <?php if($is_admin):?><div class="fg"><label>ID Autor (gol = contul tău)</label><input id="a-au" type="number" placeholder="<?=$user['id']?>"></div><?php endif;?>
  <div class="mact"><button class="btn suc" onclick="crA()">Salvează</button><button class="btn sec" onclick="CM('m-aa')">Anulează</button></div>
</div></div>

<div id="m-ea" class="mb"><div class="md"><h3>Editează articol</h3>
  <input type="hidden" id="ea-id">
  <div class="fg"><label>Titlu *</label><input id="ea-ti" type="text"></div>
  <div class="fg"><label>Rezumat</label><input id="ea-re" type="text"></div>
  <div class="fg"><label>Categorie</label><select id="ea-ca"></select></div>
  <div class="fg"><label>Status</label><select id="ea-st"><option value="draft">Draft</option><option value="publicat">Publicat</option><option value="arhivat">Arhivat</option></select></div>
  <div class="mact"><button class="btn suc" onclick="upA()">Salvează</button><button class="btn sec" onclick="CM('m-ea')">Anulează</button></div>
</div></div>

<div id="m-ac" class="mb"><div class="md"><h3>Categorie nouă</h3>
  <div class="fg"><label>Nume * (min 3)</label><input id="c-nu" type="text"></div>
  <div class="fg"><label>Descriere</label><textarea id="c-de" rows="3"></textarea></div>
  <div class="mact"><button class="btn suc" onclick="crC()">Salvează</button><button class="btn sec" onclick="CM('m-ac')">Anulează</button></div>
</div></div>

<div id="m-ec" class="mb"><div class="md"><h3>Editează categorie</h3>
  <input type="hidden" id="ec-id">
  <div class="fg"><label>Nume *</label><input id="ec-nu" type="text"></div>
  <div class="fg"><label>Descriere</label><textarea id="ec-de" rows="3"></textarea></div>
  <div class="mact"><button class="btn suc" onclick="upC()">Salvează</button><button class="btn sec" onclick="CM('m-ec')">Anulează</button></div>
</div></div>

<?php if($is_admin):?>
<div id="m-acm" class="mb"><div class="md"><h3>Comentariu nou</h3>
  <div class="fg"><label>Articol *</label><select id="cm-ar"></select></div>
  <div class="fg"><label>Text * (min 3)</label><textarea id="cm-tx" rows="4"></textarea></div>
  <div class="mact"><button class="btn suc" onclick="crCm()">Trimite</button><button class="btn sec" onclick="CM('m-acm')">Anulează</button></div>
</div></div>
<?php endif;?>

<?php if($is_admin):?>
<div id="m-aev" class="mb"><div class="md"><h3>Evaluare nouă</h3>
  <div class="fg"><label>Articol *</label><select id="ev-ar"></select></div>
  <div class="fg"><label>Notă (1-5)</label><select id="ev-no"><option value="5">5 — Excelent</option><option value="4">4 — Bun</option><option value="3" selected>3 — Mediu</option><option value="2">2 — Slab</option><option value="1">1 — Foarte slab</option></select></div>
  <div class="mact"><button class="btn suc" onclick="crEv()">Salvează</button><button class="btn sec" onclick="CM('m-aev')">Anulează</button></div>
</div></div>
<?php endif;?>

<script>
const IS_ADMIN = <?=$is_admin?'true':'false'?>;

function OM(id){document.getElementById(id).classList.add('on');}
function CM(id){document.getElementById(id).classList.remove('on');}
document.querySelectorAll('.mb').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('on');}));

function msg(txt,ok=true){const m=document.getElementById('M');m.className='msg on '+(ok?'mok':'mer');m.textContent=txt;setTimeout(()=>m.classList.remove('on'),4000);}
function sb(id,html){const e=document.getElementById(id);if(e)e.innerHTML=html;}
function v(id){const e=document.getElementById(id);return e?e.value:'';}
function jc(s){return s?`<span class="jc" title="${s.replace(/"/g,'&quot;')}">{...}</span>`:'';}
function bdg(s){const c={publicat:'pub',draft:'dra',arhivat:'arh'};return `<span class="bdg ${c[s]||'arh'}">${s}</span>`;}

async function api(params){
  const fd=new FormData();
  Object.entries(params).forEach(([k,v])=>fd.append(k,v));
  try{
    const r=await fetch('api/crud.php',{method:'POST',body:fd});
    const t=await r.text();
    try{return JSON.parse(t);}
    catch(e){return{succes:false,eroare:'Server error: '+t.substring(0,200)};}
  }catch(e){return{succes:false,eroare:'Eroare retea: '+e.message};}
}

function T(el,id){
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('on'));
  document.querySelectorAll('.pnl').forEach(p=>p.classList.remove('on'));
  el.classList.add('on');
  document.getElementById('pnl-'+id).classList.add('on');
  load(id);
}

let cats=[], arts=[];
async function loadSel(){
  const dc=await api({actiune:'citeste',tabel:'categorii'});
  cats=dc.date||[];
  ['a-ca','ea-ca'].forEach(id=>{const s=document.getElementById(id);if(s)s.innerHTML=cats.map(c=>`<option value="${c.id_categorie}">${c.nume_categorie}</option>`).join('');});
  const da=await api({actiune:'citeste',tabel:'articole_pub'});
  arts=da.date||[];
  ['cm-ar','ev-ar'].forEach(id=>{const s=document.getElementById(id);if(s)s.innerHTML=arts.map(a=>`<option value="${a.id_articol}">${a.titlu}</option>`).join('');});
}

async function load(tbl){
  if(!['utilizatori','roluri','articole','categorii','comentarii','evaluari'].includes(tbl))return;
  sb('b-'+tbl,`<tr><td colspan="9" class="ld">Se încarcă...</td></tr>`);
  const d=await api({actiune:'citeste',tabel:tbl});
  if(!d.succes){sb('b-'+tbl,`<tr><td colspan="9" style="color:red;padding:10px">${d.eroare}</td></tr>`);return;}
  if(!d.date||!d.date.length){sb('b-'+tbl,`<tr><td colspan="9" style="text-align:center;padding:20px;color:#888">Nicio înregistrare.</td></tr>`);return;}

  try{
  if(tbl==='utilizatori')sb('b-utilizatori',d.date.map(u=>`<tr>
    <td>${u.id_utilizator}</td><td><b>${u.prenume} ${u.nume}</b></td><td>${u.email}</td>
    <td><span class="bdg b1">${u.denumire_rol}</span></td>
    <td><span class="bdg ${u.activ?'bok':'bno'}">${u.activ?'Da':'Nu'}</span></td>
    <td>${(u.data_inregistrare||'').substring(0,10)}</td>
    <td>${jc(u.metadata_json)}</td>
    <td class="act">
      <button class="btn sec sm" onclick='eU(${JSON.stringify(u).replace(/'/g,"\\'")})'  >Edit</button>
      <button class="btn dan sm" onclick="del('utilizator',${u.id_utilizator})">Del</button>
    </td></tr>`).join(''));

  if(tbl==='roluri')sb('b-roluri',d.date.map(r=>`<tr>
    <td>${r.id_rol}</td><td><b>${r.denumire_rol}</b></td><td>${r.descriere||''}</td><td>${r.nr_utilizatori}</td>
    <td>${jc(r.metadata_json)}</td></tr>`).join(''));

  if(tbl==='articole')sb('b-articole',d.date.map(a=>`<tr>
    <td>${a.id_articol}</td>
    <td title="${a.titlu}">${a.titlu.substring(0,35)}${a.titlu.length>35?'…':''}</td>
    <td>${a.autor}</td><td>${a.nume_categorie}</td>
    <td>${bdg(a.status)}</td><td>${a.numar_vizualizari}</td>
    <td>${jc(a.metadata_json)}</td>
    <td class="act">
      <button class="btn sec sm" onclick='eA(${JSON.stringify(a).replace(/'/g,"\\'")})'  >Edit</button>
      <button class="btn dan sm" onclick="del('articol',${a.id_articol})">Del</button>
    </td></tr>`).join(''));

  if(tbl==='categorii')sb('b-categorii',d.date.map(c=>`<tr>
    <td>${c.id_categorie}</td><td><b>${c.nume_categorie}</b></td><td>${c.descriere||''}</td><td>${c.nr_articole}</td>
    <td>${jc(c.metadata_json)}</td>
    <td class="act">
      <button class="btn sec sm" onclick='eC(${JSON.stringify(c).replace(/'/g,"\\'")})'  >Edit</button>
      <button class="btn dan sm" onclick="del('categorie',${c.id_categorie})">Del</button>
    </td></tr>`).join(''));

  if(tbl==='comentarii')sb('b-comentarii',d.date.map(c=>`<tr>
    <td>${c.id_comentariu}</td>
    <td title="${c.titlu_articol||''}">${(c.titlu_articol||'').substring(0,25)}</td>
    <td>${c.autor||''}</td>
    <td title="${c.text_comentariu||''}">${(c.text_comentariu||'').substring(0,40)}</td>
    <td><span class="bdg ${c.aprobat?'bok':'bno'}">${c.aprobat?'Da':'Nu'}</span></td>
    <td>${(c.data_comentariu||'').substring(0,10)}</td>
    <td>${jc(c.metadata_json)}</td>
    ${IS_ADMIN?`<td class="act">
      <button class="btn ${c.aprobat?'sec':'suc'} sm" onclick="apCm(${c.id_comentariu},${c.aprobat?0:1})">${c.aprobat?'Respinge':'Aprobă'}</button>
      <button class="btn dan sm" onclick="del('comentariu',${c.id_comentariu})">Del</button>
    </td>`:''}</tr>`).join(''));

  if(tbl==='evaluari')sb('b-evaluari',d.date.map(e=>`<tr>
    <td>${e.id_evaluare}</td>
    <td title="${e.titlu_articol||''}">${(e.titlu_articol||'').substring(0,25)}</td>
    <td>${e.autor||''}</td>
    <td><b>${e.nota}/5</b></td>
    <td>${(e.data_evaluare||'').substring(0,10)}</td>
    <td>${jc(e.metadata_json)}</td>
    <td class="act"><button class="btn dan sm" onclick="del('evaluare',${e.id_evaluare})">Del</button></td>
    </tr>`).join(''));
  }catch(err){sb('b-'+tbl,`<tr><td colspan="9" style="color:red;padding:10px">Eroare randare: ${err.message}</td></tr>`);}
}

async function crU(){const d=await api({actiune:'creeaza',tabel:'utilizator',prenume:v('u-pr'),nume:v('u-nu'),email:v('u-em'),parola:v('u-pa'),id_rol:v('u-ro')});if(d.succes){msg(d.mesaj);CM('m-au');load('utilizatori');}else msg(d.eroare,false);}
function eU(u){document.getElementById('eu-id').value=u.id_utilizator;document.getElementById('eu-pr').value=u.prenume;document.getElementById('eu-nu').value=u.nume;document.getElementById('eu-em').value=u.email;document.getElementById('eu-ro').value=u.id_rol;document.getElementById('eu-ac').value=u.activ;OM('m-eu');}
async function upU(){const d=await api({actiune:'actualizeaza',tabel:'utilizator',id_utilizator:v('eu-id'),prenume:v('eu-pr'),nume:v('eu-nu'),email:v('eu-em'),id_rol:v('eu-ro'),activ:v('eu-ac')});if(d.succes){msg(d.mesaj);CM('m-eu');load('utilizatori');}else msg(d.eroare,false);}

async function crA(){const d=await api({actiune:'creeaza',tabel:'articol',titlu:v('a-ti'),rezumat:v('a-re'),continut:v('a-co'),id_categorie:v('a-ca'),status:v('a-st'),id_autor:v('a-au')||''});if(d.succes){msg(d.mesaj);CM('m-aa');load('articole');loadSel();}else msg(d.eroare,false);}
function eA(a){document.getElementById('ea-id').value=a.id_articol;document.getElementById('ea-ti').value=a.titlu;document.getElementById('ea-re').value=a.rezumat||'';document.getElementById('ea-ca').value=a.id_categorie;document.getElementById('ea-st').value=a.status;OM('m-ea');}
async function upA(){const d=await api({actiune:'actualizeaza',tabel:'articol',id_articol:v('ea-id'),titlu:v('ea-ti'),rezumat:v('ea-re'),id_categorie:v('ea-ca'),status:v('ea-st')});if(d.succes){msg(d.mesaj);CM('m-ea');load('articole');}else msg(d.eroare,false);}

async function crC(){const d=await api({actiune:'creeaza',tabel:'categorie',nume_categorie:v('c-nu'),descriere:v('c-de')});if(d.succes){msg(d.mesaj);CM('m-ac');load('categorii');loadSel();}else msg(d.eroare,false);}
function eC(c){document.getElementById('ec-id').value=c.id_categorie;document.getElementById('ec-nu').value=c.nume_categorie;document.getElementById('ec-de').value=c.descriere||'';OM('m-ec');}
async function upC(){const d=await api({actiune:'actualizeaza',tabel:'categorie',id_categorie:v('ec-id'),nume_categorie:v('ec-nu'),descriere:v('ec-de')});if(d.succes){msg(d.mesaj);CM('m-ec');load('categorii');}else msg(d.eroare,false);}

async function crCm(){const d=await api({actiune:'creeaza',tabel:'comentariu',id_articol:v('cm-ar'),text_comentariu:v('cm-tx')});if(d.succes){msg(d.mesaj);CM('m-acm');load('comentarii');}else msg(d.eroare,false);}
async function apCm(id,val){const d=await api({actiune:'actualizeaza',tabel:'comentariu',id_comentariu:id,aprobat:val});if(d.succes){msg(d.mesaj);load('comentarii');}else msg(d.eroare,false);}

async function crEv(){const d=await api({actiune:'creeaza',tabel:'evaluare',id_articol:v('ev-ar'),nota:v('ev-no')});if(d.succes){msg(d.mesaj);CM('m-aev');load('evaluari');}else msg(d.eroare,false);}

async function del(tbl,id){
  if(!confirm('Confirmați ștergerea ID '+id+'?'))return;
  const d=await api({actiune:'sterge',tabel:tbl,id});
  const pm={utilizator:'utilizatori',articol:'articole',categorie:'categorii',comentariu:'comentarii',evaluare:'evaluari'};
  if(d.succes){msg(d.mesaj);load(pm[tbl]||tbl);}else msg(d.eroare,false);
}

async function testInt(){
  const el=document.getElementById('int-r');
  el.innerHTML='<div class="ld">Se rulează testele...</div>';
  const fd=new FormData(); fd.append('actiune','test_integritate');
  try{
    const r=await fetch('api/crud.php',{method:'POST',body:fd});
    const t=await r.text();
    let d;
    try{ d=JSON.parse(t); }
    catch(e){ el.innerHTML=`<div style="color:red;padding:10px;background:#fef2f2;border-radius:8px"><b>Răspuns invalid de la server:</b><br><pre style="font-size:11px;margin-top:6px;white-space:pre-wrap">${t.substring(0,600)}</pre></div>`; return; }
    if(!d.succes){el.innerHTML=`<div style="color:red;padding:10px">${d.eroare}</div>`;return;}
    const cm={PASS:'pass',INFO:'info',WARN:'warn',FAIL:'fail'};
    const cs={PASS:'sp',INFO:'si',WARN:'sw',FAIL:'sf'};
    el.innerHTML=`<div class="sm2"><span>Total:<b> ${d.sumar.total}</b></span><span style="color:#166534">PASS:<b> ${d.sumar.pass}</b></span><span style="color:#1e40af">INFO:<b> ${d.sumar.info}</b></span><span style="color:#854d0e">WARN:<b> ${d.sumar.warn}</b></span></div>`+
    d.rezultate.map(r=>`<div class="ir ir-${cm[r.status]}"><span class="is ${cs[r.status]}">${r.status}</span><div class="ib"><div class="it">${r.test}</div><div class="ia">${r.actiune}</div><div class="iz">${r.rezultat}</div></div></div>`).join('');
  }catch(e){el.innerHTML=`<div style="color:red;padding:10px">Eroare retea: ${e.message}</div>`;}
}

// Init - sequential to avoid session lock
(async()=>{
  await loadSel();
  const ft=document.querySelector('.tab.on');
  if(ft){const id=ft.getAttribute('onclick').match(/'([^']+)'\)/)[1];load(id);}
})();
</script>
</body>
</html>
