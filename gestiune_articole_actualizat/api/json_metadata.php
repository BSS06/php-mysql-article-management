<?php
require_once __DIR__.'/../config.php';

$dbs = "'".DB1."','".DB2."','".DB3."'";

out([
    'sumar'    => q(DB1, "SELECT TABLE_SCHEMA AS baza, COUNT(DISTINCT TABLE_NAME) AS tabele, COUNT(*) AS coloane
                          FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA IN($dbs) GROUP BY TABLE_SCHEMA"),
    'tabele'   => q(DB1, "SELECT JSON_OBJECT('baza',TABLE_SCHEMA,'tabel',TABLE_NAME,
                                             'randuri',TABLE_ROWS,'kb',ROUND(DATA_LENGTH/1024,2),
                                             'motor',ENGINE,'codificare',TABLE_COLLATION) AS meta
                          FROM INFORMATION_SCHEMA.TABLES
                          WHERE TABLE_SCHEMA IN($dbs) AND TABLE_TYPE='BASE TABLE'
                          ORDER BY TABLE_SCHEMA,TABLE_NAME"),
    'coloane'  => q(DB1, "SELECT TABLE_SCHEMA AS baza, TABLE_NAME AS tabel,
                                 JSON_ARRAYAGG(JSON_OBJECT('col',COLUMN_NAME,'tip',COLUMN_TYPE,
                                                           'null',IS_NULLABLE,'cheie',COLUMN_KEY)) AS cols
                          FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA IN($dbs)
                          GROUP BY TABLE_SCHEMA,TABLE_NAME ORDER BY TABLE_SCHEMA,TABLE_NAME"),
]);
