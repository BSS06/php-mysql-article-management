<?php
require_once __DIR__.'/../config.php';
$d = q(DB3, "SELECT ROUND(AVG(nota),2) AS medie, COUNT(*) AS nr,
                    (SELECT titlu FROM ".DB2.".articole WHERE id_articol=ev.id_articol) AS titlu
             FROM evaluari ev GROUP BY id_articol ORDER BY medie DESC");
out(['obiectiv'=>'OB8','nod'=>DB3,'tip'=>'bar',
     'etichete'=>array_column($d,'titlu'),
     'seturi'  =>[
         ['label'=>'Medie notă (1-5)','valori'=>array_map('floatval',array_column($d,'medie')),'culoare'=>'#993C1D'],
         ['label'=>'Nr. evaluări',    'valori'=>array_map('intval',  array_column($d,'nr')),    'culoare'=>'#FAC775'],
     ],'brut'=>$d]);
