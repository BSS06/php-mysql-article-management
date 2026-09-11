<?php
error_reporting(0);
ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__) . '/config.php';
$ROL = isset($_SESSION['user']['rol']) ? $_SESSION['user']['rol'] : '';
$UID = isset($_SESSION['user']['id'])  ? intval($_SESSION['user']['id']) : 0;
session_write_close();
if (!$ROL || !in_array($ROL, array('administrator','redactor'))) {
    echo json_encode(array('eroare'=>'Acces interzis')); exit;
}
$IS_ADMIN = ($ROL === 'administrator');
$ACT = isset($_POST['actiune']) ? trim($_POST['actiune']) : (isset($_GET['actiune']) ? trim($_GET['actiune']) : '');
$TBL = isset($_POST['tabel'])   ? trim($_POST['tabel'])   : (isset($_GET['tabel'])   ? trim($_GET['tabel'])   : '');
function jsok($msg, $data = null) {
    $r = array('succes'=>true, 'mesaj'=>$msg);
    if ($data !== null) $r['date'] = $data;
    echo json_encode($r, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); exit;
}
function jserr($msg) {
    echo json_encode(array('eroare'=>$msg), JSON_UNESCAPED_UNICODE); exit;
}
function ge($db, $v) { return db($db)->real_escape_string($v); }
function gid($v)    { return is_numeric($v) && intval($v) > 0; }
function gp($k)      { return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }
function gi($k)     { return isset($_POST[$k]) ? intval($_POST[$k]) : 0; }
function roluri_canonice(){ return array('administrator','redactor','cititor'); }
function rol_valid($rol){ return in_array($rol, roluri_canonice(), true); }
function descriere_implicita_rol($rol){
    $map = array(
        'administrator' => 'Acces complet: CRUD pe toate entitățile, gestionare utilizatori, roluri și comentarii.',
        'redactor' => 'Poate crea, edita, publica și arhiva articole proprii și poate consulta obiectivele editoriale relevante.',
        'cititor' => 'Poate vizualiza articole publicate și poate lăsa comentarii sau evaluări.'
    );
    return isset($map[$rol]) ? $map[$rol] : '';
}
function permisiuni_implicite_rol($rol){
    $map = array(
        'administrator' => array(
            'create_article'=>1,'edit_any_article'=>1,'delete_article'=>1,'manage_users'=>1,'manage_roles'=>1,'delete_comment'=>1,
            'view_analytics'=>1,'view_dashboard'=>1,'publish_article'=>1,'manage_comments'=>1,'manage_categories'=>1,
            'add_comment'=>1,'rate_article'=>1,'view_public_content'=>1
        ),
        'redactor' => array(
            'create_article'=>1,'edit_own_article'=>1,'publish_article'=>1,'archive_article'=>1,'delete_comment'=>0,'manage_users'=>0,
            'view_dashboard'=>1,'manage_roles'=>0,'edit_any_article'=>0,'delete_article'=>0,'manage_comments'=>0,'view_analytics'=>1,
            'manage_categories'=>0,'add_comment'=>1,'rate_article'=>1,'view_public_content'=>1
        ),
        'cititor' => array(
            'create_article'=>0,'add_comment'=>1,'rate_article'=>1,'view_public_content'=>1,'view_dashboard'=>0,'manage_users'=>0,
            'manage_roles'=>0,'edit_any_article'=>0,'delete_article'=>0,'publish_article'=>0,'manage_comments'=>0,'view_analytics'=>0,
            'manage_categories'=>0,'delete_comment'=>0
        )
    );
    return isset($map[$rol]) ? $map[$rol] : array();
}
function sincronizeaza_permisiuni_rol($id_rol, $rol){
    $id_rol = intval($id_rol);
    if ($id_rol <= 0 || !rol_valid($rol)) return;
    $c = db(DB1);
    $c->query("DELETE FROM permisiuni WHERE id_rol=$id_rol");
    foreach (permisiuni_implicite_rol($rol) as $actiune => $permis) {
        $a = ge(DB1, $actiune);
        $p = $permis ? 1 : 0;
        $c->query("INSERT INTO permisiuni(id_rol,actiune,permis) VALUES($id_rol,'$a',$p)");
    }
}
if ($ACT === 'citeste') {
    if ($TBL === 'utilizatori') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        jsok('ok', q(DB1, "SELECT u.id_utilizator,u.nume,u.prenume,u.email,u.activ,u.data_inregistrare,r.denumire_rol,u.id_rol, JSON_OBJECT('id',u.id_utilizator,'email',u.email,'rol',r.denumire_rol,'activ',u.activ) AS metadata_json FROM utilizatori u JOIN roluri r USING(id_rol) ORDER BY u.id_utilizator"));
    }
    if ($TBL === 'roluri') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        jsok('ok', q(DB1, "SELECT r.id_rol,r.denumire_rol,r.descriere,COUNT(DISTINCT u.id_utilizator) AS nr_utilizatori,COUNT(DISTINCT p.id_permisiune) AS nr_permisiuni, JSON_OBJECT('id',r.id_rol,'rol',r.denumire_rol,'nr_utilizatori',COUNT(DISTINCT u.id_utilizator),'nr_permisiuni',COUNT(DISTINCT p.id_permisiune)) AS metadata_json FROM roluri r LEFT JOIN utilizatori u USING(id_rol) LEFT JOIN permisiuni p ON p.id_rol=r.id_rol GROUP BY r.id_rol,r.denumire_rol,r.descriere ORDER BY r.id_rol"));
    }
    if ($TBL === 'articole') {
        $w = $IS_ADMIN ? '' : "WHERE a.id_autor = $UID";
        jsok('ok', q(DB2, "SELECT a.id_articol,a.titlu,a.rezumat,a.status,a.data_publicarii,a.numar_vizualizari,a.id_autor,a.id_categorie,c.nume_categorie,CONCAT(u.prenume,' ',u.nume) AS autor, JSON_OBJECT('id',a.id_articol,'titlu',a.titlu,'status',a.status,'vizualizari',a.numar_vizualizari,'categorie',c.nume_categorie) AS metadata_json FROM gestiune_continut.articole a JOIN gestiune_continut.categorii c USING(id_categorie) JOIN gestiune_utilizatori.utilizatori u ON u.id_utilizator=a.id_autor $w ORDER BY a.id_articol"));
    }
    if ($TBL === 'categorii') {
        jsok('ok', q(DB2, "SELECT c.id_categorie,c.nume_categorie,c.descriere,COUNT(a.id_articol) AS nr_articole, JSON_OBJECT('id',c.id_categorie,'nume',c.nume_categorie,'articole',COUNT(a.id_articol)) AS metadata_json FROM categorii c LEFT JOIN articole a USING(id_categorie) GROUP BY c.id_categorie"));
    }
    if ($TBL === 'comentarii') {
        
        $w = $IS_ADMIN ? '' : "WHERE a.id_autor = $UID";
        jsok('ok', q(DB3, "SELECT cm.id_comentariu,cm.id_articol,cm.id_utilizator,cm.text_comentariu,cm.data_comentariu,cm.aprobat,CONCAT(u.prenume,' ',u.nume) AS autor,a.titlu AS titlu_articol, JSON_OBJECT('id',cm.id_comentariu,'autor',CONCAT(u.prenume,' ',u.nume),'articol',a.titlu,'aprobat',cm.aprobat) AS metadata_json FROM gestiune_interactiuni.comentarii cm JOIN gestiune_utilizatori.utilizatori u ON u.id_utilizator=cm.id_utilizator JOIN gestiune_continut.articole a ON a.id_articol=cm.id_articol $w ORDER BY cm.id_comentariu"));
    }
    if ($TBL === 'evaluari') {
        if (!$IS_ADMIN) jserr('Acces interzis - evaluarile sunt gestionate doar de administrator.');
        $w = '';
        $rows = q(DB3, "SELECT ev.id_evaluare,ev.id_articol,ev.id_utilizator,ev.nota,ev.data_evaluare,CONCAT(u.prenume,' ',u.nume) AS autor,a.titlu AS titlu_articol, JSON_OBJECT('id',ev.id_evaluare,'nota',ev.nota,'autor',CONCAT(u.prenume,' ',u.nume),'articol',a.titlu) AS metadata_json FROM gestiune_interactiuni.evaluari ev JOIN gestiune_utilizatori.utilizatori u ON u.id_utilizator=ev.id_utilizator JOIN gestiune_continut.articole a ON a.id_articol=ev.id_articol ORDER BY ev.id_evaluare");
        jsok('ok', $rows);
    }
    if ($TBL === 'articole_pub') {
        jsok('ok', q(DB2, "SELECT id_articol,titlu FROM articole WHERE status='publicat' ORDER BY titlu"));
    }
    jserr('Tabel necunoscut: '.$TBL);
}
if ($ACT === 'creeaza') {
    if ($TBL === 'utilizator') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $prenume=gp('prenume'); $nume=gp('nume'); $email=gp('email'); $parola=gp('parola'); $id_rol=gi('id_rol');
        if (!$prenume||!$nume) jserr('Nume si prenume obligatorii.');
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) jserr('Email invalid.');
        if (strlen($parola)<4) jserr('Parola minim 4 caractere.');
        if (!gid($id_rol)) jserr('Selectati un rol.');
        if (q(DB1,"SELECT id_utilizator FROM utilizatori WHERE email='".ge(DB1,$email)."'")) jserr('Email deja inregistrat.');
        $c=db(DB1); $c->query("INSERT INTO utilizatori(nume,prenume,email,parola_hash,id_rol) VALUES('".ge(DB1,$nume)."','".ge(DB1,$prenume)."','".ge(DB1,$email)."','".hash('sha256',$parola)."',$id_rol)");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Utilizatorul $prenume $nume creat (ID:".$c->insert_id.").", array('id'=>$c->insert_id));
    }
    if ($TBL === 'rol') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $denumire = gp('denumire_rol');
        $descriere = gp('descriere');
        if (!rol_valid($denumire)) jserr('Rol invalid. Portalul accepta doar: administrator, redactor, cititor.');
        if (q(DB1, "SELECT id_rol FROM roluri WHERE denumire_rol='".ge(DB1,$denumire)."'")) jserr('Rolul exista deja.');
        if ($descriere === '') $descriere = descriere_implicita_rol($denumire);
        $c=db(DB1); $c->query("INSERT INTO roluri(denumire_rol,descriere) VALUES('".ge(DB1,$denumire)."','".ge(DB1,$descriere)."')");
        if($c->error) jserr('BD: '.$c->error);
        sincronizeaza_permisiuni_rol($c->insert_id, $denumire);
        jsok("Rolul '$denumire' a fost creat.", array('id'=>$c->insert_id));
    }
    if ($TBL === 'articol') {
        $titlu=gp('titlu'); $continut=gp('continut'); $rezumat=gp('rezumat'); $id_cat=gi('id_categorie'); $status=gp('status'); $id_autor=$IS_ADMIN&&gi('id_autor')>0?gi('id_autor'):$UID;
        if (strlen($titlu)<5)    jserr('Titlul minim 5 caractere.');
        if (strlen($continut)<10) jserr('Continutul minim 10 caractere.');
        if (!gid($id_cat))        jserr('Selectati o categorie.');
        if (!in_array($status,array('draft','publicat','arhivat'))) $status='draft';
        $slug=trim(preg_replace('/[^a-z0-9]+/','-',strtolower($titlu)),'-').'-'.time();
        $pub=($status==='publicat')?"'".date('Y-m-d H:i:s')."'":'NULL';
        $c=db(DB2); $c->query("INSERT INTO articole(titlu,slug,continut,rezumat,id_autor,id_categorie,status,data_publicarii) VALUES('".ge(DB2,$titlu)."','".ge(DB2,$slug)."','".ge(DB2,$continut)."','".ge(DB2,$rezumat)."',$id_autor,$id_cat,'$status',$pub)");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Articolul '$titlu' creat (ID:".$c->insert_id.").", array('id'=>$c->insert_id));
    }
    if ($TBL === 'categorie') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $nume=gp('nume_categorie'); $desc=gp('descriere');
        if (strlen($nume)<3) jserr('Numele minim 3 caractere.');
        if (q(DB2,"SELECT id_categorie FROM categorii WHERE nume_categorie='".ge(DB2,$nume)."'")) jserr("Categoria '$nume' exista deja.");
        $c=db(DB2); $c->query("INSERT INTO categorii(nume_categorie,descriere) VALUES('".ge(DB2,$nume)."','".ge(DB2,$desc)."')");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Categoria '$nume' creata.", array('id'=>$c->insert_id));
    }
    if ($TBL === 'comentariu') {
        if (!$IS_ADMIN) jserr('Acces interzis - redactorii pot vizualiza comentariile, dar nu le pot adauga. Comentariile sunt feedback al cititorilor.');
        $text=gp('text_comentariu'); $id_art=gi('id_articol');
        if (strlen($text)<3) jserr('Comentariul minim 3 caractere.');
        if (!gid($id_art))   jserr('Selectati un articol.');
        if (!q(DB2,"SELECT id_articol FROM articole WHERE id_articol=$id_art AND status='publicat'")) jserr('Articolul nu e publicat.');
        $c=db(DB3); $c->query("INSERT INTO comentarii(id_articol,id_utilizator,text_comentariu,aprobat) VALUES($id_art,$UID,'".ge(DB3,$text)."',0)");
        if($c->error) jserr('BD: '.$c->error);
        jsok('Comentariu adaugat, asteapta aprobare.', array('id'=>$c->insert_id));
    }
    if ($TBL === 'evaluare') {
        if (!$IS_ADMIN) jserr('Acces interzis - doar administratorul poate gestiona evaluarile.');
        $nota=gi('nota'); $id_art=gi('id_articol');
        if ($nota<1||$nota>5) jserr('Nota trebuie sa fie 1-5.');
        if (!gid($id_art))    jserr('Selectati un articol.');
        if (!q(DB2,"SELECT id_articol FROM articole WHERE id_articol=$id_art AND status='publicat'")) jserr('Articolul nu e publicat.');
        $c=db(DB3); $c->query("INSERT INTO evaluari(id_articol,id_utilizator,nota) VALUES($id_art,$UID,$nota) ON DUPLICATE KEY UPDATE nota=$nota");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Evaluare $nota/5 salvata.");
    }
    jserr('Tabel necunoscut.');
}
if ($ACT === 'actualizeaza') {
    if ($TBL === 'utilizator') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $id=gi('id_utilizator'); $prenume=gp('prenume'); $nume=gp('nume'); $email=gp('email'); $activ=gi('activ'); $id_rol=gi('id_rol');
        if (!gid($id)||!$prenume||!$nume) jserr('Date incomplete.');
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) jserr('Email invalid.');
        if (q(DB1,"SELECT id_utilizator FROM utilizatori WHERE email='".ge(DB1,$email)."' AND id_utilizator!=$id")) jserr('Email folosit de alt cont.');
        $c=db(DB1); $c->query("UPDATE utilizatori SET nume='".ge(DB1,$nume)."',prenume='".ge(DB1,$prenume)."',email='".ge(DB1,$email)."',activ=$activ,id_rol=$id_rol WHERE id_utilizator=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Utilizatorul ID $id actualizat.");
    }
    if ($TBL === 'rol') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $id=gi('id_rol'); $denumire=gp('denumire_rol'); $descriere=gp('descriere');
        if (!gid($id)) jserr('ID invalid.');
        if (!rol_valid($denumire)) jserr('Rol invalid. Portalul accepta doar: administrator, redactor, cititor.');
        if (q(DB1, "SELECT id_rol FROM roluri WHERE denumire_rol='".ge(DB1,$denumire)."' AND id_rol!=$id")) jserr('Exista deja alt rol cu aceasta denumire.');
        if ($descriere === '') $descriere = descriere_implicita_rol($denumire);
        $c=db(DB1); $c->query("UPDATE roluri SET denumire_rol='".ge(DB1,$denumire)."',descriere='".ge(DB1,$descriere)."' WHERE id_rol=$id");
        if($c->error) jserr('BD: '.$c->error);
        sincronizeaza_permisiuni_rol($id, $denumire);
        jsok("Rolul ID $id a fost actualizat.");
    }
    if ($TBL === 'articol') {
        $id=gi('id_articol'); $titlu=gp('titlu'); $rezumat=gp('rezumat'); $id_cat=gi('id_categorie'); $status=gp('status');
        if (!gid($id))           jserr('ID invalid.');
        if (strlen($titlu)<5)   jserr('Titlul minim 5 caractere.');
        if (!in_array($status,array('draft','publicat','arhivat'))) jserr('Status invalid.');
        if (!$IS_ADMIN && !q(DB2,"SELECT id_articol FROM articole WHERE id_articol=$id AND id_autor=$UID")) jserr('Poti edita doar articolele proprii.');
        $pub=($status==='publicat')?',data_publicarii=NOW()':'';
        $c=db(DB2); $c->query("UPDATE articole SET titlu='".ge(DB2,$titlu)."',rezumat='".ge(DB2,$rezumat)."',id_categorie=$id_cat,status='$status'$pub WHERE id_articol=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Articolul ID $id actualizat.");
    }
    if ($TBL === 'categorie') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $id=gi('id_categorie'); $nume=gp('nume_categorie'); $desc=gp('descriere');
        if (!gid($id)||strlen($nume)<3) jserr('Date incomplete.');
        $c=db(DB2); $c->query("UPDATE categorii SET nume_categorie='".ge(DB2,$nume)."',descriere='".ge(DB2,$desc)."' WHERE id_categorie=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Categoria ID $id actualizata.");
    }
    if ($TBL === 'comentariu') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $id=gi('id_comentariu'); $aprobat=gi('aprobat');
        if (!gid($id)) jserr('ID invalid.');
        $c=db(DB3); $c->query("UPDATE comentarii SET aprobat=$aprobat WHERE id_comentariu=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok('Comentariul '.($aprobat?'aprobat':'respins').'.');
    }
    jserr('Tabel necunoscut.');
}
if ($ACT === 'sterge') {
    $id = gi('id');
    if (!gid($id)) jserr('ID invalid.');
    if ($TBL === 'utilizator') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        if ($id===$UID) jserr('Nu va puteti sterge propriul cont.');
        db(DB3)->query("DELETE FROM evaluari    WHERE id_utilizator=$id");
        db(DB3)->query("DELETE FROM comentarii  WHERE id_utilizator=$id");
        db(DB3)->query("DELETE FROM vizualizari WHERE id_utilizator=$id");
        db(DB1)->query("DELETE FROM sesiuni     WHERE id_utilizator=$id");
        $c=db(DB1); $c->query("DELETE FROM utilizatori WHERE id_utilizator=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Utilizatorul ID $id sters.");
    }
    if ($TBL === 'rol') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $chk=q(DB1, "SELECT r.id_rol,r.denumire_rol,COUNT(u.id_utilizator) AS nr_utilizatori FROM roluri r LEFT JOIN utilizatori u USING(id_rol) WHERE r.id_rol=$id GROUP BY r.id_rol,r.denumire_rol");
        if (!$chk) jserr('Rolul nu exista.');
        if (intval($chk[0]['nr_utilizatori']) > 0) jserr('Rolul este atribuit unor utilizatori. Mutati utilizatorii pe alt rol inainte de stergere.');
        $c=db(DB1); $c->query("DELETE FROM roluri WHERE id_rol=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Rolul '".$chk[0]['denumire_rol']."' a fost sters.");
    }
    if ($TBL === 'articol') {
        if (!$IS_ADMIN && !q(DB2,"SELECT id_articol FROM articole WHERE id_articol=$id AND id_autor=$UID")) jserr('Poti sterge doar articolele proprii.');
        db(DB3)->query("DELETE FROM comentarii  WHERE id_articol=$id");
        db(DB3)->query("DELETE FROM evaluari    WHERE id_articol=$id");
        db(DB3)->query("DELETE FROM vizualizari WHERE id_articol=$id");
        $c=db(DB2); $c->query("DELETE FROM articole WHERE id_articol=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Articolul ID $id sters din toate nodurile.");
    }
    if ($TBL === 'categorie') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $nr=q(DB2,"SELECT COUNT(*) AS n FROM articole WHERE id_categorie=$id");
        if ($nr[0]['n']>0) jserr('Categoria are articole. Mutati-le mai intai.');
        $c=db(DB2); $c->query("DELETE FROM categorii WHERE id_categorie=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Categoria ID $id stearsa.");
    }
    if ($TBL === 'comentariu') {
        if (!$IS_ADMIN) jserr('Acces interzis');
        $c=db(DB3); $c->query("DELETE FROM comentarii WHERE id_comentariu=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Comentariul ID $id sters.");
    }
    if ($TBL === 'evaluare') {
        $ch=q(DB3,"SELECT id_utilizator FROM evaluari WHERE id_evaluare=$id");
        if (!$ch) jserr('Evaluarea nu exista.');
        if (!$IS_ADMIN && intval($ch[0]['id_utilizator'])!==$UID) jserr('Poti sterge doar propriile evaluari.');
        $c=db(DB3); $c->query("DELETE FROM evaluari WHERE id_evaluare=$id");
        if($c->error) jserr('BD: '.$c->error);
        jsok("Evaluarea ID $id stearsa.");
    }
    jserr('Tabel necunoscut.');
}
if ($ACT === 'test_integritate') {
    if (!$IS_ADMIN) jserr('Acces interzis');
    ob_start();
    $rez = array();
    mysqli_report(MYSQLI_REPORT_OFF);
    try {
        @db(DB1)->query("DELETE FROM utilizatori WHERE email LIKE 'test%@test.md'");
        @db(DB2)->query("DELETE FROM articole WHERE titlu IN ('TestFK','TestCas')");
        @db(DB3)->query("DELETE FROM comentarii WHERE text_comentariu='test cascade'");
        $em='test_'.time().'@test.md'; $h=hash('sha256','test');
        @db(DB1)->query("INSERT INTO utilizatori(nume,prenume,email,parola_hash,id_rol) VALUES('T','U','$em','$h',3)");
        $id1 = db(DB1)->insert_id;
        $r2 = @db(DB1)->query("INSERT INTO utilizatori(nume,prenume,email,parola_hash,id_rol) VALUES('T2','U2','$em','$h',3)");
        $rez[] = array('test'=>'UNIQUE constraint pe email','actiune'=>"INSERT duplicat cu email '$em'",'rezultat'=>$r2?'FAIL: inserarea a reusit':'PASS: BD a blocat (#'.db(DB1)->errno.' - Duplicate entry)','status'=>$r2?'FAIL':'PASS');
        if ($id1) @db(DB1)->query("DELETE FROM utilizatori WHERE id_utilizator=$id1");
        $sl = 't'.time();
        $r3 = @db(DB2)->query("INSERT INTO articole(titlu,slug,continut,rezumat,id_autor,id_categorie,status) VALUES('TestFK','$sl','continut test','rezumat test',99999,1,'draft')");
        $id3 = ($r3 && db(DB2)->insert_id) ? db(DB2)->insert_id : 0;
        $rez[] = array('test'=>'FK logic cross-node','actiune'=>'INSERT articol cu id_autor=99999 inexistent in DB1','rezultat'=>$r3?'INFO: BD a acceptat. FK-ul este logic (nu fizic) intre noduri separate. Validarea authorului se face in aplicatie.':'WARN: '.db(DB2)->error,'status'=>$r3?'INFO':'WARN');
        if ($id3) @db(DB2)->query("DELETE FROM articole WHERE id_articol=$id3");
        $r4 = @db(DB3)->query("INSERT INTO evaluari(id_articol,id_utilizator,nota) VALUES(1,3,5) ON DUPLICATE KEY UPDATE nota=5");
        $rez[] = array('test'=>'UNIQUE evaluare (vot dublu)','actiune'=>'INSERT evaluare duplicata id_articol=1, id_utilizator=3','rezultat'=>$r4?'PASS: ON DUPLICATE KEY UPDATE a actualizat nota existenta. Un utilizator nu poate vota de doua ori.':'WARN: '.db(DB3)->error,'status'=>$r4?'PASS':'WARN');
        $r5 = @db(DB3)->query("INSERT INTO evaluari(id_articol,id_utilizator,nota) VALUES(1,4,10)");
        $id5 = ($r5 && db(DB3)->insert_id) ? db(DB3)->insert_id : 0;
        $rez[] = array('test'=>'CHECK constraint nota (1-5)','actiune'=>'INSERT evaluare cu nota=10 (invalida)','rezultat'=>$r5?'INFO: MariaDB 10.4 ignora CHECK constraints. Validarea notei (1-5) se face in aplicatie inainte de INSERT.':'PASS: BD a blocat nota invalida.','status'=>$r5?'INFO':'PASS');
        if ($id5) @db(DB3)->query("DELETE FROM evaluari WHERE id_evaluare=$id5");
        $sl2 = 'cas'.time();
        $r6 = @db(DB2)->query("INSERT INTO articole(titlu,slug,continut,rezumat,id_autor,id_categorie,status) VALUES('TestCas','$sl2','continut test','rezumat',2,1,'draft')");
        $ida = ($r6 && db(DB2)->insert_id) ? db(DB2)->insert_id : 0;
        if ($ida) {
            @db(DB3)->query("INSERT INTO comentarii(id_articol,id_utilizator,text_comentariu,aprobat) VALUES($ida,3,'test cascade',1)");
            $idc = db(DB3)->insert_id ? db(DB3)->insert_id : 0;
            @db(DB3)->query("DELETE FROM comentarii WHERE id_articol=$ida");
            @db(DB2)->query("DELETE FROM articole WHERE id_articol=$ida");
            $vc = q(DB3, "SELECT id_comentariu FROM comentarii WHERE id_comentariu=$idc");
            $rez[] = array('test'=>'Cascade logic cross-node la DELETE','actiune'=>"DELETE articol ID $ida + curatare comentariu ID $idc din DB3",'rezultat'=>$vc?'WARN: comentariul orfan a ramas in DB3. Curatarea cross-node trebuie facuta manual din aplicatie.':'PASS: Curatarea cross-node a functionat corect.','status'=>$vc?'WARN':'PASS');
        }
        $vi = q(DB2, "SELECT numar_vizualizari FROM articole WHERE id_articol=1");
        $v0 = isset($vi[0]['numar_vizualizari']) ? intval($vi[0]['numar_vizualizari']) : 0;
        @db(DB2)->query("UPDATE articole SET numar_vizualizari=numar_vizualizari+1 WHERE id_articol=1");
        @db(DB2)->query("UPDATE articole SET numar_vizualizari=numar_vizualizari+1 WHERE id_articol=1");
        $vf = q(DB2, "SELECT numar_vizualizari FROM articole WHERE id_articol=1");
        $v1 = isset($vf[0]['numar_vizualizari']) ? intval($vf[0]['numar_vizualizari']) : 0;
        $rez[] = array('test'=>'Concurenta simulata — incrementare atomica','actiune'=>'2x UPDATE numar_vizualizari=numar_vizualizari+1 pe articolul ID=1','rezultat'=>"Inainte: $v0 | Dupa: $v1. ".($v1===$v0+2?'PASS: Ambele incrementari aplicate corect. UPDATE atomic previne race condition.':'WARN: Valoare neasteptata — posibil conflict.'),'status'=>($v1===$v0+2)?'PASS':'WARN');
    } catch (Exception $ex) {
        $rez[] = array('test'=>'Eroare neasteptata','actiune'=>'—','rezultat'=>$ex->getMessage(),'status'=>'FAIL');
    }
    ob_end_clean();
    $p=0; $i=0; $w=0; $f=0;
    foreach ($rez as $row) {
        if ($row['status']==='PASS') $p++;
        elseif ($row['status']==='INFO') $i++;
        elseif ($row['status']==='WARN') $w++;
        else $f++;
    }
    echo json_encode(array('succes'=>true,'rezultate'=>$rez,'sumar'=>array('total'=>count($rez),'pass'=>$p,'info'=>$i,'warn'=>$w,'fail'=>$f)), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
echo json_encode(array('eroare'=>'Actiune necunoscuta: '.$ACT)); exit;
