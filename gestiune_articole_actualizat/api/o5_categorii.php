<?php
require_once __DIR__.'/../config.php';

$d = q(DB2, "SELECT c.nume_categorie, COUNT(a.id_articol) AS nr
             FROM categorii c LEFT JOIN articole a USING(id_categorie)
             GROUP BY c.id_categorie ORDER BY nr DESC");

out(['obiectiv'=>'OB5','nod'=>DB2,'tip'=>'bar',
     'etichete'=>array_column($d,'nume_categorie'),
     'valori'  =>array_map('intval',array_column($d,'nr')),
     'culoare' =>'#0F6E56','brut'=>$d]);
