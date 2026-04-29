<?php
if(session_status() === PHP_SESSION_NONE) session_start();

function utilizator_curent(){
    return $_SESSION['user'] ?? null;
}

function rol_curent(){
    return $_SESSION['user']['rol'] ?? '';
}

function necesita_autentificare(){
    if(!isset($_SESSION['user'])){
        header('Location: login.php');
        exit;
    }
}

function poate($actiune){
    return !empty($_SESSION['permisiuni'][$actiune]);
}
