<?php
$conn = new mysqli('127.0.0.1', 'root', 'root', 'gestiune_utilizatori', 3306);
$conn->set_charset('utf8mb4');
$hash = hash('sha256', 'root');
$ok   = $conn->query("UPDATE utilizatori SET parola_hash = '$hash'");
$nr   = $conn->affected_rows;
$conn->close();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Reset parole</title>
<style>
body{font-family:Arial,sans-serif;background:#12151f;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.box{background:#1a1e2e;border:1px solid #242840;border-radius:12px;padding:32px 36px;text-align:center;max-width:360px}
h2{color:#fff;margin-bottom:12px;font-size:18px}
p{color:#9090a8;font-size:14px;margin-bottom:20px}
.ok{color:#5dcaa5;font-weight:700;font-size:16px;margin-bottom:16px}
.accounts{text-align:left;border-top:1px solid #242840;padding-top:16px;margin-top:4px}
.row{display:flex;justify-content:space-between;padding:5px 0;font-size:13px;border-bottom:1px solid #1e2235}
.row:last-child{border:none}
.row span:first-child{color:#ccd}
.row span:last-child{color:#7c6fcd;font-weight:700}
.btn{display:inline-block;margin-top:20px;background:#7c6fcd;color:#fff;text-decoration:none;padding:9px 24px;border-radius:8px;font-weight:700;font-size:14px}
.err{color:#f09595;font-weight:700}
</style>
</head>
<body>
<div class="box">
  <h2>Reset parole</h2>
  <?php if($ok && $nr > 0): ?>
    <p class="ok">✓ <?= $nr ?> conturi actualizate cu succes.</p>
    <div class="accounts">
      <div class="row"><span>admin@portal.md</span><span>root</span></div>
      <div class="row"><span>redactor@portal.md</span><span>root</span></div>
      <div class="row"><span>andrei@portal.md</span><span>root</span></div>
      <div class="row"><span>elena@portal.md</span><span>root</span></div>
    </div>
    <a href="login.php" class="btn">Mergi la Login</a>
  <?php else: ?>
    <p class="err">Eroare: <?= $conn->error ?? 'Nicio inregistrare actualizata.' ?></p>
  <?php endif; ?>
</div>
</body>
</html>
