<?php
require_once __DIR__.'/../config.php';
$rol = $_GET['rol'] ?? null;
if ($rol && !in_array($rol, ['administrator','redactor','cititor']))
    die(out(['eroare'=>'Rol invalid']));
$filtru = $rol ? "AND r.denumire_rol='".db(DB2)->real_escape_string($rol)."'" : '';
out([
    'rol_filtrat' => $rol ?? 'toate',
    'activi_cu_articol' => q(DB2,
        "SELECT u.id_utilizator, CONCAT(u.prenume,' ',u.nume) AS autor, u.email, r.denumire_rol AS rol,
                (SELECT COUNT(*) FROM ".DB2.".articole WHERE id_autor=u.id_utilizator AND status='publicat') AS publicate,
                (SELECT COUNT(*) FROM ".DB3.".comentarii    WHERE id_utilizator=u.id_utilizator AND aprobat=1) AS comentarii
         FROM ".DB1.".utilizatori u JOIN ".DB1.".roluri r USING(id_rol)
         WHERE u.activ=1 $filtru
           AND EXISTS (SELECT 1 FROM ".DB2.".articole WHERE id_autor=u.id_utilizator)
         ORDER BY publicate DESC"),
    'peste_medie' => q(DB2,
        "SELECT a.id_articol, a.titlu,
                ROUND((SELECT AVG(nota) FROM ".DB3.".evaluari WHERE id_articol=a.id_articol),2) AS medie_articol,
                ROUND((SELECT AVG(nota) FROM ".DB3.".evaluari),2) AS medie_globala
         FROM ".DB2.".articole a WHERE a.status='publicat'
           AND (SELECT AVG(nota) FROM ".DB3.".evaluari WHERE id_articol=a.id_articol)
             > (SELECT AVG(nota) FROM ".DB3.".evaluari)
         ORDER BY medie_articol DESC"),
]);
