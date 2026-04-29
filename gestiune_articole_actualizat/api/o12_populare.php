<?php
require_once __DIR__.'/../config.php';

$d = q(DB2, "SELECT a.titlu,
                    COUNT(DISTINCT cm.id_comentariu) AS comentarii,
                    ROUND(AVG(ev.nota),2)            AS medie,
                    a.numar_vizualizari              AS vizualizari
             FROM ".DB2.".articole a
             LEFT JOIN ".DB3.".comentarii cm ON cm.id_articol=a.id_articol AND cm.aprobat=1
             LEFT JOIN ".DB3.".evaluari    ev ON ev.id_articol=a.id_articol
             WHERE a.status='publicat'
             GROUP BY a.id_articol ORDER BY comentarii DESC, medie DESC");

out(['obiectiv'=>'OB12','nod'=>'DB2×DB3','tip'=>'bar',
     'etichete'=>array_column($d,'titlu'),
     'seturi'  =>[
         ['label'=>'Comentarii', 'valori'=>array_map('intval',  array_column($d,'comentarii')), 'culoare'=>'#534AB7'],
         ['label'=>'Medie notă', 'valori'=>array_map('floatval',array_column($d,'medie')),      'culoare'=>'#0F6E56'],
         ['label'=>'Vizualizări','valori'=>array_map('intval',  array_column($d,'vizualizari')),'culoare'=>'#993C1D'],
     ],'brut'=>$d]);
