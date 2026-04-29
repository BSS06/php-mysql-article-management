<?php
if(session_status() === PHP_SESSION_NONE) session_start();

if(isset($_SESSION['user'])){
    header('Location: index.php');
    exit;
}

$eroare = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email  = trim($_POST['email']  ?? '');
    $parola = trim($_POST['parola'] ?? '');

    if($email && $parola){
        $conn = new mysqli('127.0.0.1', 'root', 'root', 'gestiune_utilizatori', 3306);
        $conn->set_charset('utf8mb4');

        if(!$conn->connect_error){
            $e    = $conn->real_escape_string($email);
            $hash = hash('sha256', $parola);

            $res = $conn->query(
                "SELECT u.id_utilizator, u.nume, u.prenume, u.email,
                        r.denumire_rol, u.id_rol
                 FROM utilizatori u
                 JOIN roluri r ON r.id_rol = u.id_rol
                 WHERE u.email = '$e'
                   AND u.parola_hash = '$hash'
                   AND u.activ = 1
                 LIMIT 1"
            );

            if($res && $res->num_rows === 1){
                $u = $res->fetch_assoc();

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
                $eroare = 'Email sau parola incorecta, sau contul este inactiv.';
            }
            $conn->close();
        } else {
            $eroare = 'Eroare conexiune BD: ' . $conn->connect_error;
        }
    } else {
        $eroare = 'Completati email-ul si parola.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Autentificare — Portal Editorial</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#12151f;display:flex;align-items:center;justify-content:center;min-height:100vh}
.box{background:#1a1e2e;border:1px solid #242840;border-radius:14px;padding:40px 36px;width:380px}
.logo{width:36px;height:36px;background:#7c6fcd;border-radius:9px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px}
.logo svg{width:20px;height:20px;fill:#fff}
h1{font-size:20px;font-weight:700;color:#fff;text-align:center;margin-bottom:4px}
.sub{font-size:13px;color:#6c7086;text-align:center;margin-bottom:26px}
label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9090a8;margin-bottom:5px}
input[type=email],input[type=password]{width:100%;background:#12151f;border:1px solid #2a2f50;border-radius:8px;padding:10px 12px;font-size:14px;color:#fff;outline:none;margin-bottom:14px;font-family:Arial,sans-serif}
input:focus{border-color:#7c6fcd}
input::placeholder{color:#444}
.btn{width:100%;background:#7c6fcd;color:#fff;border:none;border-radius:8px;padding:11px;font-size:14px;font-weight:700;cursor:pointer;margin-top:4px;font-family:Arial,sans-serif}
.btn:hover{background:#6a5ec0}
.err{background:#2a1520;border:1px solid #5a1a2a;border-radius:8px;padding:10px 12px;font-size:13px;color:#f09595;margin-bottom:16px}
.hints{margin-top:22px;border-top:1px solid #242840;padding-top:16px}
.hl{font-size:11px;color:#555;margin-bottom:8px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
.hr{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #1e2235;font-size:12px}
.hr:last-child{border:none}
.hr span:first-child{color:#ccd}
.rt{font-size:10px;padding:2px 8px;border-radius:4px;font-weight:600}
.ra{background:#2a2550;color:#a89de8}
.rr{background:#0a2e20;color:#5dcaa5}
.rc{background:#2e1a10;color:#f0a080}
.pw{display:flex;justify-content:space-between;padding-top:8px;font-size:12px;color:#555}
.pw span:last-child{color:#7c6fcd;font-weight:700}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <svg viewBox="0 0 18 18"><rect x="1" y="1" width="7" height="7" rx="1.5"/><rect x="10" y="1" width="7" height="7" rx="1.5"/><rect x="1" y="10" width="7" height="7" rx="1.5"/><rect x="10" y="10" width="7" height="4" rx="1.5"/><rect x="13" y="16" width="4" height="1.5" rx=".75"/></svg>
  </div>
  <h1>Portal Editorial</h1>
  <p class="sub">Autentificati-va pentru a continua</p>

  <?php if($eroare): ?>
  <div class="err"><?= htmlspecialchars($eroare) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Email</label>
    <input type="email" name="email" placeholder="email@portal.md"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
    <label>Parola</label>
    <input type="password" name="parola" placeholder="Parola dvs." required>
    <button type="submit" class="btn">Autentificare</button>
  </form>

  <div class="hints">
    <p class="hl">Conturi demo</p>
    <div class="hr"><span>admin@portal.md</span><span class="rt ra">Administrator</span></div>
    <div class="hr"><span>redactor@portal.md</span><span class="rt rr">Redactor</span></div>
    <div class="hr"><span>andrei@portal.md</span><span class="rt rc">Cititor</span></div>
    <div class="pw"><span>Parola (dupa UPDATE SQL):</span><span>root</span></div>
  </div>
</div>
</body>
</html>
