<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
necesita_autentificare();
$user = utilizator_curent();
$rol  = rol_curent();

if ($rol === 'cititor') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/procese/context.php';
require_once __DIR__ . '/procese/chart_helpers.php';
$allTasks = require __DIR__ . '/procese/task_index.php';
$tasks    = array_filter($allTasks, fn($t) => in_array($rol, $t['roles'], true));
$taskIds  = array_keys($tasks);

$currentId = $_GET['task'] ?? ($taskIds[0] ?? '');
if (!isset($tasks[$currentId])) $currentId = $taskIds[0] ?? '';
$current = $tasks[$currentId] ?? null;

$ctx    = procese_context($user);
function procese_run_task(string $file, array $ctx): string {
    ob_start(); include $file; return trim(ob_get_clean());
}
$output = $current ? procese_run_task($current['file'], $ctx)  : 'Niciun obiectiv disponibil.';
$source = $current ? file_get_contents($current['file'])        : '';
$chart  = $current ? procese_build_chart($currentId, $ctx)     : ['title'=>'Context editorial','items'=>[],'max'=>1,'accent'=>'violet'];

$scopeCounts = $ctx['scope']['counts']      ?? [];
$scopeTop    = $ctx['scope']['top_article'] ?? [];
$scopeLabel  = $ctx['scope']['label']       ?? 'global';

$groups = [];
foreach ($tasks as $id => $task) $groups[$task['group']][$id] = $task;

$roleBadge = [
    'administrator' => ['sfx'=>'adm','lbl'=>'Administrator'],
    'redactor'      => ['sfx'=>'red','lbl'=>'Redactor'],
][$rol] ?? ['sfx'=>'cit','lbl'=>ucfirst($rol)];

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Obiective POO — Portal Editorial</title>
<style>
:root{--sb:#12151f;--sb2:#1a1e2e;--sb3:#242840;--acc:#7c6fcd;--bg:#f0f2f8;--card:#fff;--tx:#1a1a2e;--tx2:#5a5a7a;--tx3:#9090a8;--bd:#e4e6f0;--r:10px}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:var(--bg);color:var(--tx);display:flex;height:100vh;overflow:hidden}
a{text-decoration:none}
.sb{width:255px;min-width:255px;background:var(--sb);display:flex;flex-direction:column;height:100vh}
.brand{padding:18px 16px 14px;border-bottom:1px solid #1f2235;flex-shrink:0}
.bmark{width:34px;height:34px;background:var(--acc);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.bmark svg{width:18px;height:18px;fill:#fff}
.brand h1{font-size:13px;font-weight:700;color:#fff}
.brand p{font-size:11px;color:var(--tx3);margin-top:2px}
.role-banner{padding:6px 14px;font-size:11px;font-weight:600;text-align:center;flex-shrink:0}
.rb-adm{background:rgba(83,74,183,.25);color:#a89de8;border-bottom:1px solid rgba(83,74,183,.3)}
.rb-red{background:rgba(15,110,86,.2);color:#5dcaa5;border-bottom:1px solid rgba(15,110,86,.25)}
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
.ni-lbl{font-size:12px;color:#bbb;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ni.on .ni-lbl{color:#fff;font-weight:500}
.ni-src{font-size:9px;color:#555}
.ni.on .ni-src{color:rgba(255,255,255,.5)}
.ni-bg{font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;flex-shrink:0}
.bs1{background:rgba(124,111,205,.2);color:#a99ee8}
.bs2{background:rgba(15,110,86,.2);color:#5dcaa5}
.nores{display:none;padding:14px 10px;font-size:12px;color:#7e859f}
.user-card{padding:12px 14px;border-top:1px solid #1f2235;flex-shrink:0;display:flex;align-items:center;gap:10px}
.u-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.u-adm{background:#534AB7;color:#fff}
.u-red{background:#0F6E56;color:#fff}
.u-name{font-size:12px;font-weight:600;color:#ccd;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u-rol{font-size:10px;color:#666;margin-top:1px}
.hbtn{background:none;border:none;cursor:pointer;color:#555;font-size:11px;padding:4px 6px;border-radius:5px;transition:.15s;text-decoration:none;display:inline-block}
.hbtn:hover{background:#2a1520;color:#f09595}
.hbtn-acc{color:#7c6fcd!important}
.hbtn-acc:hover{background:#1f2235!important;color:#a99ee8!important}
/* ── Role Switcher ── */
.sw-wrap{position:relative;flex-shrink:0}
.sw-trigger{background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;gap:10px;flex:1;min-width:0;text-align:left;width:100%}
.sw-caret{font-size:9px;color:#555;transition:transform .18s;flex-shrink:0}
.sw-wrap.open .sw-caret{transform:rotate(180deg)}
.sw-popup{display:none;position:absolute;bottom:calc(100% + 6px);left:0;right:0;background:#1a1e2e;border:1px solid #2a2f50;border-radius:10px;overflow:hidden;box-shadow:0 8px 28px rgba(0,0,0,.5);z-index:999}
.sw-wrap.open .sw-popup{display:block}
.sw-head{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555;padding:9px 12px 6px}
.sw-item{display:flex;align-items:center;gap:9px;padding:8px 12px;cursor:pointer;transition:background .12s;border:none;background:none;width:100%;text-align:left;font-family:Arial,sans-serif}
.sw-item:hover{background:#242840}
.sw-item.active{background:rgba(124,111,205,.12)}
.sw-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0}
.sw-adm{background:#534AB7;color:#fff}
.sw-red{background:#0F6E56;color:#fff}
.sw-cit{background:#7A2F15;color:#fff}
.sw-info{flex:1;min-width:0}
.sw-lbl{font-size:12px;font-weight:600;color:#ccd}
.sw-sub{font-size:10px;color:#666;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sw-check{font-size:12px;color:#7c6fcd;flex-shrink:0}
.sw-sep{height:1px;background:#1f2235;margin:2px 0}
.sw-out{display:flex;align-items:center;gap:6px;padding:7px 12px;cursor:pointer;font-size:11px;color:#666;transition:background .12s;text-decoration:none;border:none;background:none;width:100%;font-family:Arial,sans-serif}
.sw-out:hover{background:#2a1520;color:#f09595}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0}
.top{background:var(--card);border-bottom:1px solid var(--bd);padding:0 26px;height:50px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;gap:14px}
.top-t{font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.top-sub{font-size:11px;color:var(--tx2);margin-top:2px}
.pills{display:flex;gap:6px;flex-shrink:0}
.pill{font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px}
.pp1{background:rgba(124,111,205,.1);color:#5047a0;border:1px solid rgba(124,111,205,.2)}
.pp2{background:rgba(15,110,86,.1);color:#0a5038;border:1px solid rgba(15,110,86,.2)}
.pp3{background:rgba(153,60,29,.1);color:#7a2810;border:1px solid rgba(153,60,29,.2)}
.workspace{flex:1;display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:18px;padding:20px 26px;overflow:hidden}
.center-col,.right-col{min-width:0;overflow-y:auto;padding-right:2px}
.center-col::-webkit-scrollbar,.right-col::-webkit-scrollbar{width:5px}
.center-col::-webkit-scrollbar-thumb,.right-col::-webkit-scrollbar-thumb{background:var(--bd);border-radius:3px}
.kpi-g{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.kpi-c{background:var(--card);border:1px solid var(--bd);border-radius:var(--r);padding:13px 15px}
.kpi-c.ac{border-top:3px solid var(--acc)}
.kpi-v{font-size:24px;font-weight:700;color:var(--tx)}
.kpi-l{font-size:11px;color:var(--tx2);margin-top:4px}
.kpi-s{font-size:10px;color:var(--tx3);margin-top:2px}
.card{background:var(--card);border:1px solid var(--bd);border-radius:var(--r);padding:16px 18px;margin-bottom:14px}
.ct{font-size:12px;font-weight:600;color:var(--tx2);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:6px}
.ct-l{display:flex;align-items:center;gap:6px}
.ci{width:18px;height:18px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}
.code-wrap{margin-bottom:0}
.sql-toggle{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#0d1117;border-radius:8px;cursor:pointer;user-select:none;border:1px solid #21262d}
.sql-toggle:hover{background:#161b22}
.sql-arrow{font-size:11px;color:var(--acc);transition:transform .2s}
.sql-label{font-size:11px;font-weight:600;color:#8b949e;flex:1}
.sql-body{display:none;background:#0d1117;border:1px solid #21262d;border-top:none;border-radius:0 0 8px 8px;padding:14px 16px}
.sql-body.open{display:block}
.sql-body pre{font-family:'Courier New',monospace;font-size:12px;line-height:1.7;white-space:pre-wrap;word-break:break-word;color:#e6edf3;margin:0}
.result-card{background:#f7f8fc}
.result-card pre{font-family:'Courier New',monospace;font-size:12.5px;line-height:1.7;white-space:pre-wrap;word-break:break-word;color:#24304a;margin:0}
.chart-card{position:sticky;top:0}
.chart-title{font-size:13px;font-weight:700;color:var(--tx);margin-bottom:12px}
.chart-bars{display:flex;flex-direction:column;gap:10px}
.bar-row{display:flex;flex-direction:column;gap:5px}
.bar-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px}
.bar-meta span:first-child{color:var(--tx2);max-width:68%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bar-meta strong{color:var(--tx)}
.bar-track{height:10px;border-radius:999px;background:#eef1fa;overflow:hidden;border:1px solid #e2e6f3}
.bar-fill{height:100%;border-radius:999px}
.bar-fill.violet{background:linear-gradient(90deg,#7c6fcd,#9b90e5)}
.bar-fill.green{background:linear-gradient(90deg,#0f6e56,#36b58c)}
.bar-fill.orange{background:linear-gradient(90deg,#9b3c1d,#e48b52)}
.scope-dl{display:grid;gap:8px}
.scope-dl div{padding:9px 11px;background:#f7f8fc;border:1px solid #ebedf6;border-radius:8px}
.scope-dl dt{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--tx3);margin-bottom:3px}
.scope-dl dd{font-size:13px;color:var(--tx);font-weight:600}
.empty{color:var(--tx2);font-size:13px}
</style>
</head>
<body>

<aside class="sb">
  <div class="brand">
    <div class="bmark">
      <svg viewBox="0 0 18 18"><rect x="1" y="1" width="7" height="7" rx="1.5"/><rect x="10" y="1" width="7" height="7" rx="1.5"/><rect x="1" y="10" width="7" height="7" rx="1.5"/><rect x="10" y="10" width="7" height="4" rx="1.5"/><rect x="13" y="16" width="4" height="1.5" rx=".75"/></svg>
    </div>
    <h1>Portal Editorial</h1>
    <p>Obiective POO — <?= h($scopeLabel) ?></p>
  </div>

  <div class="role-banner rb-<?= h($roleBadge['sfx']) ?>">
    Conectat ca: <?= h($roleBadge['lbl']) ?>
  </div>

  <div class="srch">
    <input type="search" id="taskSearch" placeholder="Caută obiectiv..." autocomplete="off">
  </div>

  <nav id="taskNav">
    <?php foreach ($groups as $groupName => $items): ?>
      <div class="ng" data-group><?= h($groupName) ?></div>
      <?php foreach ($items as $id => $task): ?>
        <a class="ni <?= $id === $currentId ? 'on' : '' ?>" data-task
           data-search="<?= h(strtolower($task['label'].' '.$task['title'].' '.($task['keywords']??''))) ?>"
           href="?task=<?= urlencode($id) ?>">
          <div class="ni-dot"></div>
          <div style="flex:1;min-width:0">
            <div class="ni-lbl"><?= h($task['label']) ?></div>
            <div class="ni-src"><?= h($task['source']) ?></div>
          </div>
          <span class="ni-bg <?= str_starts_with($id,'s1') ? 'bs1' : 'bs2' ?>">
            <?= str_starts_with($id,'s1') ? 'S1' : 'S2' ?>
          </span>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <div class="nores" id="noResults">Niciun obiectiv găsit.</div>
  </nav>

  <div class="user-card" style="padding:0;flex-direction:column;align-items:stretch;gap:0">
    <div class="sw-wrap" id="swWrap">
      <button type="button" class="sw-trigger" style="padding:12px 14px" onclick="document.getElementById('swWrap').classList.toggle('open')">
        <div class="u-avatar u-<?= h($roleBadge['sfx']) ?>"><?= h(strtoupper(substr($user['nume'],0,2))) ?></div>
        <div style="flex:1;min-width:0">
          <div class="u-name"><?= h($user['nume']) ?></div>
          <div class="u-rol"><?= h($user['email']) ?></div>
        </div>
        <span class="sw-caret">▲</span>
      </button>
      <div class="sw-popup">
        <div class="sw-head">Schimbă rolul</div>
        <?php
        $roluri_sw = [
          1 => ['lbl'=>'Administrator','email'=>'admin@portal.md',   'cls'=>'sw-adm','ini'=>'AD'],
          2 => ['lbl'=>'Redactor',     'email'=>'redactor@portal.md','cls'=>'sw-red','ini'=>'RE'],
          3 => ['lbl'=>'Cititor',      'email'=>'cititor@portal.md', 'cls'=>'sw-cit','ini'=>'CI'],
        ];
        foreach($roluri_sw as $rid => $ri):
          $isCurrent = ($rid === (int)$user['id_rol']);
        ?>
        <form method="POST" action="switch_rol.php" style="display:contents">
          <input type="hidden" name="id_rol" value="<?= $rid ?>">
          <input type="hidden" name="redirect" value="obiective_editoriale.php">
          <button type="submit" class="sw-item<?= $isCurrent?' active':'' ?>"<?= $isCurrent?' disabled':'' ?>>
            <div class="sw-dot <?= $ri['cls'] ?>"><?= $ri['ini'] ?></div>
            <div class="sw-info">
              <div class="sw-lbl"><?= $ri['lbl'] ?></div>
              <div class="sw-sub"><?= $ri['email'] ?></div>
            </div>
            <?php if($isCurrent): ?><span class="sw-check">✓</span><?php endif; ?>
          </button>
        </form>
        <?php endforeach; ?>
        <div class="sw-sep"></div>
        <a href="logout.php" class="sw-out"><span style="font-size:13px">⏻</span> Deconectare</a>
      </div>
    </div>
    <div style="display:flex;gap:0;border-top:1px solid #1f2235">
      <a href="index.php" class="hbtn hbtn-acc" style="flex:1;text-align:center;padding:7px">⌂ Portal</a>
      <?php if (in_array($rol, ['administrator','redactor'], true)): ?>
      <a href="crud.php" class="hbtn hbtn-acc" style="flex:1;text-align:center;padding:7px;border-left:1px solid #1f2235">⚙ CRUD</a>
      <?php endif; ?>
    </div>
  </div>
</aside>
<script>
document.addEventListener('click', function(e){
  var w = document.getElementById('swWrap');
  if(w && !w.contains(e.target)) w.classList.remove('open');
});
</script>

<div class="main">
  <div class="top">
    <div style="min-width:0">
      <div class="top-t"><?= h($current['title'] ?? 'Obiective editoriale POO') ?></div>
      <div class="top-sub"><?= h($current['source'] ?? 'Programare Orientată pe Obiecte — PHP') ?></div>
    </div>
    <div class="pills">
      <span class="pill pp1"><?= h($current['group'] ?? 'Sarcini') ?></span>
      <span class="pill pp2"><?= h($scopeLabel) ?></span>
      <span class="pill pp3"><?= h($roleBadge['lbl']) ?></span>
    </div>
  </div>

  <div class="workspace">
    <section class="center-col">
      <div class="kpi-g">
        <div class="kpi-c ac">
          <div class="kpi-v"><?= h($scopeCounts['published_count'] ?? 0) ?></div>
          <div class="kpi-l">Publicate</div>
          <div class="kpi-s">în scope curent</div>
        </div>
        <div class="kpi-c">
          <div class="kpi-v"><?= h($scopeCounts['approved_comments'] ?? 0) ?></div>
          <div class="kpi-l">Comentarii</div>
          <div class="kpi-s">aprobate</div>
        </div>
        <div class="kpi-c ac">
          <div class="kpi-v"><?= h(number_format((float)($scopeCounts['avg_rating'] ?? 0), 2)) ?></div>
          <div class="kpi-l">Rating mediu</div>
          <div class="kpi-s">din 5 stele</div>
        </div>
        <div class="kpi-c">
          <div class="kpi-v"><?= h($scopeCounts['active_sessions'] ?? 0) ?></div>
          <div class="kpi-l">Sesiuni active</div>
          <div class="kpi-s">în sistem</div>
        </div>
      </div>

      <?php if ($current): ?>
      <div class="card">
        <div class="ct">
          <div class="ct-l">
            <div class="ci" style="background:#0d1117;color:#7c6fcd">{ }</div>
            Cod PHP — <?= h($current['source']) ?>
          </div>
        </div>
        <div class="code-wrap">
          <div class="sql-toggle" onclick="toggleCode(this)">
            <span class="sql-arrow">▶</span>
            <span class="sql-label">Afișează / ascunde codul sursă</span>
            <span style="font-size:10px;color:#555">click pentru a vedea</span>
          </div>
          <div class="sql-body"><pre><?= h($source) ?></pre></div>
        </div>
      </div>

      <div class="card result-card">
        <div class="ct">
          <div class="ct-l">
            <div class="ci" style="background:#f0f2f8;color:#5a5a7a">▶</div>
            Rezultat execuție
          </div>
        </div>
        <pre><?= h($output !== '' ? $output : '(fără output)') ?></pre>
      </div>
      <?php else: ?>
      <div class="card"><p class="empty">Niciun obiectiv disponibil pentru acest rol.</p></div>
      <?php endif; ?>
    </section>

    <aside class="right-col">
      <div class="card chart-card">
        <div class="chart-title"><?= h($chart['title']) ?></div>
        <div class="chart-bars">
          <?php foreach (($chart['items'] ?? []) as $item):
            $val   = (float)($item['value'] ?? 0);
            $max   = (float)($chart['max']   ?? 1);
            $width = max(4, (int)round((abs($val) / max($max, 1)) * 100));
            $accent = in_array($chart['accent'] ?? 'violet', ['violet','green','orange'], true) ? $chart['accent'] : 'violet';
          ?>
          <div class="bar-row">
            <div class="bar-meta">
              <span><?= h($item['label'] ?? '') ?></span>
              <strong><?= h($item['display'] ?? $val) ?></strong>
            </div>
            <div class="bar-track"><div class="bar-fill <?= h($accent) ?>" style="width:<?= $width ?>%"></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="ct">
          <div class="ct-l">
            <div class="ci" style="background:#eef0fa;color:#5047a0">★</div>
            Rezumat editorial
          </div>
        </div>
        <dl class="scope-dl">
          <div><dt>Articol de referință</dt><dd><?= h($scopeTop['titlu']            ?? '—') ?></dd></div>
          <div><dt>Autor</dt>              <dd><?= h($scopeTop['autor']             ?? '—') ?></dd></div>
          <div><dt>Categorie</dt>          <dd><?= h($scopeTop['nume_categorie']    ?? '—') ?></dd></div>
          <div><dt>Vizualizări</dt>        <dd><?= h($scopeTop['numar_vizualizari'] ?? 0)   ?></dd></div>
        </dl>
      </div>
    </aside>
  </div>
</div>

<script>
(function(){
  const input = document.getElementById('taskSearch');
  const nav   = document.getElementById('taskNav');
  if (!input || !nav) return;
  const items  = Array.from(nav.querySelectorAll('[data-task]'));
  const groups = Array.from(nav.querySelectorAll('[data-group]'));
  const noRes  = document.getElementById('noResults');
  function filter() {
    const q = input.value.trim().toLowerCase();
    let vis = 0;
    items.forEach(el => {
      const show = !q || (el.getAttribute('data-search')||'').includes(q);
      el.style.display = show ? '' : 'none';
      if (show) vis++;
    });
    groups.forEach(g => {
      let has = false;
      let s = g.nextElementSibling;
      while (s && !s.hasAttribute('data-group')) {
        if (s.hasAttribute('data-task') && s.style.display !== 'none') has = true;
        s = s.nextElementSibling;
      }
      g.style.display = has ? '' : 'none';
    });
    noRes.style.display = vis ? 'none' : 'block';
  }
  input.addEventListener('input', filter);
})();

function toggleCode(el) {
  const body  = el.nextElementSibling;
  const arrow = el.querySelector('.sql-arrow');
  const hint  = el.querySelector('span:last-child');
  const open  = body.classList.toggle('open');
  arrow.textContent = open ? '▼' : '▶';
  if (hint) hint.textContent = open ? '' : 'click pentru a vedea';
}
</script>
</body>
</html>
