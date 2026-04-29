<?php
require_once __DIR__.'/../config.php';

$d = q(DB1, "SELECT r.denumire_rol, COUNT(u.id_utilizator) AS nr
             FROM roluri r LEFT JOIN utilizatori u USING(id_rol)
             GROUP BY r.id_rol ORDER BY nr DESC");

out(['obiectiv'=>'OB2','nod'=>DB1,'tip'=>'pie',
     'etichete'=>array_column($d,'denumire_rol'),
     'valori'  =>array_map('intval',array_column($d,'nr')),
     'culori'  =>['#534AB7','#0F6E56','#993C1D'],
     'brut'    =>$d]);
