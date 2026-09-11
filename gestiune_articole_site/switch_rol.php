<?php

if(session_status() === PHP_SESSION_NONE) session_start();

if(!isset($_SESSION['user'])){
    header('Location: login.php'); exit;
}

$id_rol   = (int)($_POST['id_rol']   ?? 0);
$redirect = $_POST['redirect'] ?? 'index.php';

$redirect = basename(preg_replace('/[^a-zA-Z0-9_.\-]/', '', $redirect));
if(!preg_match('/\.php$/', $redirect) || !file_exists(__DIR__.'/'.$redirect)){
    $redirect = 'index.php';
}

if($id_rol >= 1 && $id_rol <= 3){
    $conn = new mysqli('127.0.0.1', 'root', '', 'gestiune_utilizatori', 3306);
    $conn->set_charset('utf8mb4');
    if(!$conn->connect_error){
        $res = $conn->query(
            "SELECT u.id_utilizator, u.nume, u.prenume, u.email,
                    r.denumire_rol, u.id_rol
             FROM utilizatori u
             JOIN roluri r ON r.id_rol = u.id_rol
             WHERE u.id_rol = $id_rol AND u.activ = 1
             ORDER BY u.id_utilizator ASC LIMIT 1"
        );
        if($res && $res->num_rows === 1){
            $u  = $res->fetch_assoc();
            $pr = $conn->query(
                "SELECT actiune, permis FROM permisiuni WHERE id_rol = {$u['id_rol']}"
            );
            $perm = [];
            while($p = $pr->fetch_assoc()){
                $perm[$p['actiune']] = (bool)$p['permis'];
            }
            $conn->query(
                "UPDATE utilizatori SET ultima_autentificare = NOW()
                 WHERE id_utilizator = {$u['id_utilizator']}"
            );
            $_SESSION['user'] = [
                'id'     => $u['id_utilizator'],
                'nume'   => $u['prenume'] . ' ' . $u['nume'],
                'email'  => $u['email'],
                'rol'    => $u['denumire_rol'],
                'id_rol' => $u['id_rol'],
            ];
            $_SESSION['permisiuni'] = $perm;
        }
        $conn->close();
    }
}

$newRol = $_SESSION['user']['rol'] ?? '';
$protejate = ['crud.php' => ['administrator','redactor'],
              'obiective_editoriale.php' => ['administrator','redactor']];
if(isset($protejate[$redirect]) && !in_array($newRol, $protejate[$redirect])){
    $redirect = 'index.php';
}

header('Location: ' . $redirect);
exit;
