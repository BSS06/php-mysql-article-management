<?php
require_once __DIR__.'/../config.php';
$d = q(DB3, "SELECT DATE(data_vizualizare) AS zi, COUNT(*) AS nr
             FROM vizualizari
             WHERE data_vizualizare >= CURDATE() - INTERVAL 7 DAY
             GROUP BY zi ORDER BY zi");
if (!$d) $d = q(DB3, "SELECT data_stat AS zi, SUM(vizualizari_zi) AS nr
                       FROM statistici_zilnice GROUP BY data_stat ORDER BY data_stat LIMIT 7");
out(['obiectiv'=>'OB10','nod'=>DB3,'tip'=>'line',
     'etichete'=>array_column($d,'zi'),
     'valori'  =>array_map('intval',array_column($d,'nr')),
     'culoare' =>'#534AB7','brut'=>$d]);
