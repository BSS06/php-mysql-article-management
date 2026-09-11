<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', 3306);
if(!defined('DB1')) define('DB1','gestiune_utilizatori');
if(!defined('DB2')) define('DB2','gestiune_continut');
if(!defined('DB3')) define('DB3','gestiune_interactiuni');
if(!function_exists('db')){
    function db($b){
        static $p=[];
        if(!isset($p[$b])){
            $c=@new mysqli(DB_HOST,DB_USER,DB_PASS,$b,DB_PORT);
            if($c->connect_error){
                header('Content-Type: application/json; charset=utf-8');
                die(json_encode(['eroare'=>'Conexiune BD: '.$c->connect_error]));
            }
            $c->set_charset('utf8mb4');
            $p[$b]=$c;
        }
        return $p[$b];
    }
}
if(!function_exists('q')){
    function q($b,$s){
        $r=db($b)->query($s);
        if(!$r) return [];
        $a=[];
        while($row=$r->fetch_assoc()) $a[]=$row;
        return $a;
    }
}
if(!function_exists('out')){
    function out($d){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
}
