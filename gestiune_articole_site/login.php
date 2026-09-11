<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

$eroare = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_rol = (int)($_POST['id_rol'] ?? 0);
    if($id_rol >= 1 && $id_rol <= 3){
        $conn = new mysqli('127.0.0.1', 'root', '', 'gestiune_utilizatori', 3306);
        $conn->set_charset('utf8mb4');
        if(!$conn->connect_error){
            $res = $conn->query(
                "SELECT u.id_utilizator, u.nume, u.prenume, u.email,
                        r.denumire_rol, u.id_rol
                 FROM utilizatori u
                 JOIN roluri r ON r.id_rol = u.id_rol
                 WHERE u.id_rol = $id_rol
                   AND u.activ = 1
                 ORDER BY u.id_utilizator ASC
                 LIMIT 1"
            );
            if($res && $res->num_rows === 1){
                $u  = $res->fetch_assoc();
                $pr = $conn->query(
                    "SELECT actiune, permis FROM permisiuni WHERE id_rol = {$u['id_rol']}"
                );
                $perm = [];
                while($p = $pr->fetch_assoc()){
                    $perm[$p['actiune']] = (bool)$p['permis'];
                }
                $conn->query(
                    "UPDATE utilizatori SET ultima_autentificare = NOW()
                     WHERE id_utilizator = {$u['id_utilizator']}"
                );
                $_SESSION['user'] = [
                    'id'     => $u['id_utilizator'],
                    'nume'   => $u['prenume'] . ' ' . $u['nume'],
                    'email'  => $u['email'],
                    'rol'    => $u['denumire_rol'],
                    'id_rol' => $u['id_rol'],
                ];
                $_SESSION['permisiuni'] = $perm;
                $conn->close();
                header('Location: index.php');
                exit;
            } else {
                $eroare = 'Nu există utilizatori activi pentru rolul ales.';
            }
            $conn->close();
        } else {
            $eroare = 'Eroare conexiune BD: ' . $conn->connect_error;
        }
    } else {
        $eroare = 'Rol invalid.';
    }
}

$roluri = [
    1 => [
        'eticheta'   => 'Administrator',
        'descriere'  => 'Acces complet: CRUD articole, gestionare utilizatori, roluri și analize.',
        'permisiuni' => ['CRUD complet','Gestionare utilizatori','Gestionare roluri','Analize & statistici'],
        'cls'        => 'ra',
        'icon'       => '<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    ],
    2 => [
        'eticheta'   => 'Redactor',
        'descriere'  => 'Crează, editează, publică și arhivează articolele proprii.',
        'permisiuni' => ['Creare articole','Editare articole proprii','Publicare & arhivare','Vizualizare analize'],
        'cls'        => 'rr',
        'icon'       => '<path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
    ],
    3 => [
        'eticheta'   => 'Cititor',
        'descriere'  => 'Vizualizează articolele publicate, lasă comentarii și evaluări.',
        'permisiuni' => ['Vizualizare articole','Adăugare comentarii','Evaluare articole'],
        'cls'        => 'rc',
        'icon'       => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    ],
];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Intrare — Portal Editorial</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:
.wrap{width:100%;max-width:740px}
.hd{text-align:center;margin-bottom:36px}
.logo{width:42px;height:42px;background:
.logo svg{width:22px;height:22px;fill:none;stroke:
h1{font-size:22px;font-weight:700;color:
.sub{font-size:13px;color:
.err{background:
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media(max-width:600px){.grid{grid-template-columns:1fr}}
.card{background:
.card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.35)}
.card:active{transform:translateY(0)}
.card.ra{--ac:
.card.rr{--ac:
.card.rc{--ac:
.card:hover{border-color:var(--ac)}
.ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px}
.card.ra .ico{background:rgba(124,111,205,.18)}
.card.rr .ico{background:rgba(15,180,120,.18)}
.card.rc .ico{background:rgba(240,130,80,.18)}
.ico svg{width:22px;height:22px;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.card.ra .ico svg{stroke:
.card.rr .ico svg{stroke:
.card.rc .ico svg{stroke:
.badge{display:inline-block;font-size:10px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;padding:3px 9px;border-radius:5px;margin-bottom:10px}
.card.ra .badge{background:rgba(124,111,205,.2);color:
.card.rr .badge{background:rgba(15,180,120,.15);color:
.card.rc .badge{background:rgba(240,130,80,.15);color:
.ct{font-size:15px;font-weight:700;color:
.cd{font-size:12px;color:
.pl{list-style:none;padding:0}
.pl li{font-size:11px;color:
.pl li::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0;background:var(--ac);opacity:.7}
.btn{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;margin-top:16px;background:var(--ac);color:
.btn:hover{opacity:1}
.btn svg{width:14px;height:14px;fill:none;stroke:
.card:hover .btn svg{transform:translateX(3px)}
.ft{text-align:center;margin-top:28px;font-size:11px;color:
</style>
</head>
<body>
<div class="wrap">
  <div class="hd">
    <div class="logo">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="4" rx="1.5"/><rect x="17.5" y="20" width="3.5" height="1.5" rx=".75"/></svg>
    </div>
    <h1>Portal Editorial</h1>
    <p class="sub">Alegeți rolul pentru a intra în portal</p>
  </div>

  <?php if($eroare): ?>
  <div class="err"><?= htmlspecialchars($eroare) ?></div>
  <?php endif; ?>

  <div class="grid">
    <?php foreach($roluri as $id => $r): ?>
    <form method="POST" style="display:contents">
      <input type="hidden" name="id_rol" value="<?= $id ?>">
      <button type="submit" class="card <?= $r['cls'] ?>">
        <div class="ico">
          <svg viewBox="0 0 24 24"><?= $r['icon'] ?></svg>
        </div>
        <span class="badge"><?= $r['eticheta'] ?></span>
        <div class="ct"><?= $r['eticheta'] ?></div>
        <div class="cd"><?= $r['descriere'] ?></div>
        <ul class="pl">
          <?php foreach($r['permisiuni'] as $p): ?><li><?= htmlspecialchars($p) ?></li><?php endforeach; ?>
        </ul>
        <div class="btn">
          Intră ca <?= $r['eticheta'] ?>
          <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </div>
      </button>
    </form>
    <?php endforeach; ?>
  </div>

  <p class="ft">Portal Editorial &mdash; acces demo fără parolă</p>
</div>
</body>
</html>
