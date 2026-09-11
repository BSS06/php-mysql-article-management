<?php
error_reporting(0);
ini_set('display_errors',0);
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../config.php';
if(!utilizator_curent()){
    out(['eroare'=>'Neautentificat']);
    exit;
}
$rol_user=rol_curent();
function acces_interzis(){
    out(['eroare'=>'Acces interzis pentru rolul dvs.']);
    exit;
}
function verifica_rol($roluri_permise){
    global $rol_user;
    if(!in_array($rol_user, $roluri_permise)) acces_interzis();
}
$ob=trim($_GET['ob']??'dashboard');
$rs='';
if(trim($_GET['rol']??'') && in_array(trim($_GET['rol']), ['administrator','redactor','cititor'])){
    $rs = db(DB1)->real_escape_string(trim($_GET['rol']));
}
function resp($t,$n,$d,$s,$rows,$c,$k){
    out(['tip'=>'obiectiv','titlu'=>$t,'nod'=>$n,'descriere'=>$d,'sql'=>trim($s),
         'coloane'=>$rows?array_keys($rows[0]):[],'date'=>$rows,'chart'=>$c,'kpi'=>$k]);
}
function bar($d,$l,$v,$col){
    return['tip'=>'bar','etichete'=>array_column($d,$l),'valori'=>array_map('floatval',array_column($d,$v)),'culoare'=>$col];
}
if($ob==='dashboard'){
    try{
        $kpi=q(DB1,"SELECT (SELECT COUNT(*) FROM ".DB1.".utilizatori) AS utilizatori,(SELECT COUNT(*) FROM ".DB2.".articole WHERE status='publicat') AS publicate,(SELECT COUNT(*) FROM ".DB2.".articole WHERE status='draft') AS draft,(SELECT COUNT(*) FROM ".DB3.".comentarii WHERE aprobat=1) AS comentarii,(SELECT ROUND(AVG(nota),2) FROM ".DB3.".evaluari) AS medie_nota,(SELECT COUNT(*) FROM ".DB3.".vizualizari) AS vizualizari");
        $kpi=$kpi[0]??['utilizatori'=>0,'publicate'=>0,'draft'=>0,'comentarii'=>0,'medie_nota'=>0,'vizualizari'=>0];
        $rol=q(DB1,"SELECT r.denumire_rol,COUNT(u.id_utilizator) AS nr FROM roluri r LEFT JOIN utilizatori u USING(id_rol) GROUP BY r.id_rol ORDER BY nr DESC");
        $cat=q(DB2,"SELECT c.nume_categorie,COUNT(a.id_articol) AS nr FROM categorii c LEFT JOIN articole a USING(id_categorie) GROUP BY c.id_categorie ORDER BY nr DESC");
        $top=q(DB2,"SELECT a.titlu,a.numar_vizualizari FROM articole a WHERE a.status='publicat' ORDER BY a.numar_vizualizari DESC LIMIT 8");
        $eval=q(DB3,"SELECT ROUND(AVG(nota),2) AS medie,(SELECT titlu FROM ".DB2.".articole WHERE id_articol=ev.id_articol) AS titlu FROM evaluari ev GROUP BY id_articol ORDER BY medie DESC LIMIT 8");
        $stat=q(DB3,"SELECT data_stat AS zi, SUM(vizualizari_zi) AS nr FROM statistici_zilnice GROUP BY data_stat ORDER BY data_stat LIMIT 10");
        $status_art=q(DB2,"SELECT status, COUNT(*) AS nr FROM articole GROUP BY status");
        out(['tip'=>'dashboard','kpi'=>$kpi,
             'chart_roluri'=>['tip'=>'pie','etichete'=>array_column($rol,'denumire_rol'),'valori'=>array_map('intval',array_column($rol,'nr')),'culori'=>['#7c6fcd','#0F6E56','#993C1D']],
             'chart_categorii'=>bar($cat,'nume_categorie','nr','#0F6E56'),
             'chart_top'=>bar($top,'titlu','numar_vizualizari','#7c6fcd'),
             'chart_eval'=>bar($eval,'titlu','medie','#993C1D'),
             'chart_stat'=>['tip'=>'line','etichete'=>array_column($stat,'zi'),'valori'=>array_map('intval',array_column($stat,'nr')),'culoare'=>'#BA7517'],
             'chart_status'=>['tip'=>'pie','etichete'=>array_column($status_art,'status'),'valori'=>array_map('intval',array_column($status_art,'nr')),'culori'=>['#0F6E56','#BA7517','#993C1D','#6b6b7a']],
        ]);
    }catch(Exception $e){
        out(['eroare'=>'Eroare dashboard: '.$e->getMessage()]);
    }
    exit;
}
if($ob==='1'){
    verifica_rol(['administrator']);
    $s="SELECT u.id_utilizator,u.nume,u.prenume,u.email,r.denumire_rol,u.activ,u.data_inregistrare FROM utilizatori u JOIN roluri r USING(id_rol) WHERE u.activ=1 ORDER BY r.denumire_rol,u.nume";
    $d=q(DB1,$s);$n=count($d);$a=count(array_filter($d,function($r){return $r['denumire_rol']==='administrator';}));
    $kpi="Portalul editorial functioneaza cu $n conturi active distribuite in trei niveluri: administrator, redactor si cititor. Aceasta structura tripartita reflecta modelul operational al portalului: administratorii controleaza datele sensibile din gestiune_utilizatori (parole hash, permisiuni granulare per rol), redactorii produc continut in gestiune_continut, iar cititorii genereaza interactiunile din gestiune_interactiuni. ".($a>2?"Numarul de $a administratori depaseste pragul recomandat. Un administrator are acces la tabelele roluri, permisiuni, sesiuni si parola_hash. Recomandare: verificati care administratori au sesiune activa in tabelul sesiuni si dezactivati conturile neutilizate.":"Cu $a administrator activ, portalul respecta principiul privilegiului minim. Tabelul permisiuni confirma ca accesul la functiile critice (manage_users, delete_article, view_analytics) este restrictionat corect, protejand integritatea continutului editorial si datele personale ale cititorilor.");
    resp('O1 - Lista utilizatorilor cu rolul atribuit','DB1','Toti utilizatorii activi cu rolul atribuit.',$s,$d,null,$kpi);
    exit;
}
if($ob==='2'){
    verifica_rol(['administrator']);
    $s="SELECT r.denumire_rol,COUNT(u.id_utilizator) AS nr_utilizatori FROM roluri r LEFT JOIN utilizatori u USING(id_rol) GROUP BY r.id_rol ORDER BY nr_utilizatori DESC";
    $d=q(DB1,$s);$tot=array_sum(array_column($d,'nr_utilizatori'));$cit=0;$red=0;
    foreach($d as $row){if($row['denumire_rol']==='cititor')$cit=$row['nr_utilizatori'];if($row['denumire_rol']==='redactor')$red=$row['nr_utilizatori'];}
    $rap=$red>0?round($cit/$red,1):0;
    $kpi="Portalul inregistreaza $tot utilizatori cu un raport cititori/redactor de {$rap}:1. Un redactor produce continut pentru $rap cititori inregistrati. ".($rap>=3?"Raportul justifica activitatea editoriala. Fiecare articol publicat in gestiune_continut ajunge la o audienta suficienta. Cresterea numarului de cititori inregistrati este prioritatea strategica, deoarece acestia genereaza date in gestiune_interactiuni: comentarii, evaluari, vizualizari trasabile.":"Raportul sub 3:1 indica un dezechilibru: portalul produce continut mai rapid decat creste audienta. Redactorii scriu pentru o comunitate prea mica, ceea ce reduce vizibilitatea fiecarui articol. Activati inregistrarea prin e-mail si adaugati un call-to-action de abonare pe pagina fiecarui articol publicat.");
    resp('O2 - Distributia utilizatorilor pe roluri','DB1','Echilibrul numeric intre roluri.',$s,$d,
        ['tip'=>'pie','etichete'=>array_column($d,'denumire_rol'),'valori'=>array_map('intval',array_column($d,'nr_utilizatori')),'culori'=>['#7c6fcd','#0F6E56','#993C1D']],$kpi);
    exit;
}
if($ob==='3'){
    verifica_rol(['administrator']);
    $s="SELECT id_utilizator,nume,prenume,email,activ,ultima_autentificare,data_inregistrare FROM utilizatori WHERE activ=0 OR ultima_autentificare IS NULL ORDER BY data_inregistrare DESC";
    $d=q(DB1,$s);$n=count($d);$inact=count(array_filter($d,function($r){return $r['activ']==0;}));$noauth=count(array_filter($d,function($r){return $r['ultima_autentificare']===null;}));
    $kpi="$n conturi identificate cu probleme: $inact dezactivate (activ=0) si $noauth fara nicio autentificare (ultima_autentificare IS NULL). Un cont de redactor inactiv cu parola hash veche poate fi compromis fara ca nimeni sa observe, deoarece nu exista autentificari recente in tabelul sesiuni. Un cont de administrator neutilizat cu permisiunile manage_users si delete_article active reprezinta o vulnerabilitate directa la tot continutul portalului. Identificati conturile cu rol de redactor sau administrator si dezactivati-le din phpMyAdmin daca nu sunt reclamate in 7 zile. Implementati expirarea automata a tokenurilor in tabelul sesiuni prin campul expira_la.";
    resp('O3 - Utilizatori inactivi sau fara autentificare','DB1','Conturi dezactivate sau care nu s-au autentificat niciodata.',$s,$d,null,$kpi);
    exit;
}
if($ob==='4'){
    $s="SELECT a.id_articol,a.titlu,a.data_publicarii,a.numar_vizualizari,c.nume_categorie FROM articole a JOIN categorii c USING(id_categorie) WHERE a.status='publicat' ORDER BY a.data_publicarii DESC";
    $d=q(DB2,$s);$n=count($d);$viz=array_sum(array_column($d,'numar_vizualizari'));$med=$n>0?round($viz/$n,1):0;
    $kpi="$n articole active in portal cu $viz vizualizari totale inregistrate in gestiune_interactiuni.vizualizari (medie $med vizualizari/articol). Fiecare articol are slug unic, categorie si autor asociat in gestiune_continut, permitand analize precise: care categorie aduce cel mai mult trafic, care redactor produce continutul cel mai citit. ".($med<5?"Media de $med vizualizari/articol indica ca portalul nu este descoperit de audienta potentiala. Verificati daca slugul fiecarui articol este optimizat, daca campul rezumat este completat si daca data_publicarii coincide cu perioadele de interes ale audientei.":"Media de $med vizualizari/articol confirma trafic consistent. Prioritatea urmatoare: conversia vizitatorilor anonimi (id_utilizator IS NULL in tabelul vizualizari) in cititori inregistrati care lasa comentarii si evaluari.");
    resp('O4 - Articolele publicate cu categoria','DB2','Inventarul complet al continutului public.',$s,$d,null,$kpi);
    exit;
}
if($ob==='5'){
    verifica_rol(['administrator','redactor']);
    $s="SELECT c.nume_categorie,COUNT(a.id_articol) AS nr_articole FROM categorii c LEFT JOIN articole a USING(id_categorie) GROUP BY c.id_categorie ORDER BY nr_articole DESC";
    $d=q(DB2,$s);$goale=count(array_filter($d,function($r){return $r['nr_articole']==0;}));$max=max(array_column($d,'nr_articole'));
    $kpi="$goale categorii fara niciun articol din ".count($d)." definite in gestiune_continut.categorii. Categoriile goale afecteaza structura navigatiei portalului. Concentrarea pe o singura categorie cu $max articole creeaza o dependenta periculoasa: daca acel subiect isi pierde relevanta, traficul portalului scade proportional. O distributie echilibrata extinde audienta potentiala si reduce riscul editorial. Planificati minimum un articol per categorie goala in luna curenta.";
    resp('O5 - Numarul de articole per categorie','DB2','Distributia continutului pe domenii tematice.',$s,$d,
        bar($d,'nume_categorie','nr_articole','#0F6E56'),$kpi);
    exit;
}
if($ob==='6'){
    $s="SELECT a.titlu,a.numar_vizualizari,c.nume_categorie,a.data_publicarii FROM articole a JOIN categorii c USING(id_categorie) WHERE a.status='publicat' ORDER BY a.numar_vizualizari DESC LIMIT 3";
    $d=q(DB2,$s);$top=$d[0]['titlu']??'N/A';$topv=$d[0]['numar_vizualizari']??0;$topc=$d[0]['nume_categorie']??'N/A';
    $kpi="Liderul de trafic: '$top' din categoria $topc cu $topv vizualizari. Acesta este cel mai valoros asset editorial al portalului in prezent. Redactorii trebuie sa analizeze ce anume il face performant: lungimea textului, stilul titlului, momentul publicarii (data_publicarii din gestiune_continut), categoria sau combinatia de etichete. Continut similar trebuie planificat activ. Articolele din top 3 trebuie monitorizate saptamanal in gestiune_interactiuni.vizualizari pentru a detecta scaderi brusce care ar putea indica probleme de indexare sau relevanta in scadere.";
    resp('O6 - Top 3 articole dupa vizualizari','DB2','Cel mai performant continut publicat.',$s,$d,
        bar($d,'titlu','numar_vizualizari','#7c6fcd'),$kpi);
    exit;
}
if($ob==='7'){
    verifica_rol(['administrator','redactor']);
    $s="SELECT a.id_articol,a.titlu,a.data_modificarii,a.id_autor FROM articole a WHERE a.status='draft' ORDER BY a.data_modificarii DESC";
    $d=q(DB2,$s);$n=count($d);
    $kpi="$n articole blocate in gestiune_continut cu status draft. Fiecare draft reprezinta munca editoriala investita dar continut care nu genereaza nicio valoare: nici vizualizari in gestiune_interactiuni.vizualizari, nici comentarii, nici evaluari. Campul data_modificarii arata cand a fost atins ultima data fiecare draft. Articolele nedeschise de peste 7 zile trebuie evaluate de administrator: fie publicate imediat, fie arhivate. Volumul ridicat de drafturi indica un blocaj de aprobare sau un proces de revizie prea lung. Limitati numarul de drafturi simultane per redactor la maximum 2 pentru a forta finalizarea inainte de a incepe un articol nou.";
    resp('O7 - Articole draft cu autorul responsabil','DB2','Continut creat dar nepublicat.',$s,$d,null,$kpi);
    exit;
}
if($ob==='8'){
    $s="SELECT ROUND(AVG(nota),2) AS medie_nota,COUNT(*) AS nr_evaluari,(SELECT titlu FROM ".DB2.".articole WHERE id_articol=ev.id_articol) AS titlu_articol FROM evaluari ev GROUP BY id_articol ORDER BY medie_nota DESC";
    $d=q(DB3,$s);$med=$d?round(array_sum(array_column($d,'medie_nota'))/count($d),2):0;$sub=count(array_filter($d,function($r){return $r['medie_nota']<3.5;}));
    $kpi="Media globala a evaluarilor in gestiune_interactiuni.evaluari: {$med}/5. $sub articole primesc o nota sub 3.5 din 5. Sistemul de evaluare (nota CHECK intre 1 si 5, unic per utilizator/articol prin UNIQUE KEY uq_eval_art_util) ofera feedback autentic din partea audientei. O nota sub 3.5 poate indica un titlu care promite mai mult decat livreaza sau un format greu de parcurs pe dispozitive mobile. Administratorul trebuie sa revizuiasca personal articolele sub prag inainte ca redactorul sa faca modificari, deoarece perspectiva externa este esentiala pentru un diagnostic corect.";
    resp('O8 - Media evaluarilor per articol','DB3','Scorul de calitate perceput de cititori.',$s,$d,
        ['tip'=>'bar','etichete'=>array_column($d,'titlu_articol'),'seturi'=>[['label'=>'Medie nota (1-5)','valori'=>array_map('floatval',array_column($d,'medie_nota')),'culoare'=>'#993C1D'],['label'=>'Nr. evaluari','valori'=>array_map('intval',array_column($d,'nr_evaluari')),'culoare'=>'#FAC775']]],$kpi);
    exit;
}
if($ob==='9'){
    $s="SELECT p.id_comentariu,LEFT(p.text_comentariu,60) AS comentariu,p.id_articol,COUNT(r.id_comentariu) AS nr_raspunsuri FROM comentarii p LEFT JOIN comentarii r ON r.comentariu_parinte=p.id_comentariu WHERE p.comentariu_parinte IS NULL AND p.aprobat=1 GROUP BY p.id_comentariu,comentariu,p.id_articol ORDER BY nr_raspunsuri DESC";
    $d=q(DB3,$s);$cuResp=count(array_filter($d,function($r){return $r['nr_raspunsuri']>0;}));$totResp=array_sum(array_column($d,'nr_raspunsuri'));
    $chart=bar($d,'id_comentariu','nr_raspunsuri','#BA7517');$chart['etichete']=array_map(function($r){return 'Com #'.$r['id_comentariu'];},$d);
    $kpi=count($d)." comentarii principale active, $cuResp cu cel putin un raspuns, $totResp raspunsuri totale in gestiune_interactiuni.comentarii. Structura de threading (campul comentariu_parinte) permite discutii ramificate, semn ca portalul a creat un spatiu de dialog real. Comentariile cu multe raspunsuri sunt adesea intrebari ramase fara raspuns oficial din partea redactiei, o oportunitate ratata de fidelizare. Redactorul care a scris articolul ar trebui sa raspunda la cel putin 3 comentarii per articol publicat: aceasta interactiune directa transforma un cititor ocazional in vizitator recurent si creste numarul de vizualizari inregistrate in gestiune_interactiuni.vizualizari.";
    resp('O9 - Fire de discutie: comentarii cu raspunsuri','DB3','Structura de threading a discutiilor active.',$s,$d,$chart,$kpi);
    exit;
}
if($ob==='10'){
    verifica_rol(['administrator','redactor']);
    $s="SELECT DATE(data_vizualizare) AS zi,COUNT(*) AS nr_vizualizari FROM vizualizari WHERE data_vizualizare>=CURDATE()-INTERVAL 7 DAY GROUP BY zi ORDER BY zi";
    $d=q(DB3,$s);
    if(empty($d))$d=q(DB3,"SELECT data_stat AS zi,SUM(vizualizari_zi) AS nr_vizualizari FROM statistici_zilnice GROUP BY data_stat ORDER BY data_stat LIMIT 7");
    $tot=array_sum(array_column($d,'nr_vizualizari'));$vals=array_column($d,'nr_vizualizari');$trend='';
    if(count($vals)>=2){$prim=array_slice($vals,0,3);$ult=array_slice($vals,-3);$trend=array_sum($ult)>array_sum($prim)?'ascendent':'descendent';}
    $kpi="$tot vizualizari inregistrate in gestiune_interactiuni.vizualizari in ultimele 7 zile. Trend: ".($trend?:' insuficiente date').". Tabelul vizualizari stocheaza individual fiecare accesare cu ip_adresa, user_agent si durata_sec, date granulare care permit analiza comportamentului cititorilor. Vizualizarile anonime (id_utilizator IS NULL) indica cititori neregistrati care vad continutul dar nu pot lasa comentarii sau evaluari. Conversia lor in conturi inregistrate este mecanismul principal de crestere a indicatorilor de engagement ai portalului.";
    resp('O10 - Vizualizari din ultimele 7 zile pe zi','DB3','Tendinta zilnica a traficului.',$s,$d,
        ['tip'=>'line','etichete'=>array_column($d,'zi'),'valori'=>array_map('intval',$vals),'culoare'=>'#7c6fcd'],$kpi);
    exit;
}
if($ob==='11'){
    verifica_rol(['administrator']);
    $s="SELECT u.id_utilizator,CONCAT(u.prenume,' ',u.nume) AS autor,r.denumire_rol,COUNT(a.id_articol) AS articole_publicate FROM ".DB1.".utilizatori u JOIN ".DB1.".roluri r USING(id_rol) JOIN ".DB2.".articole a ON a.id_autor=u.id_utilizator AND a.status='publicat' GROUP BY u.id_utilizator,autor,r.denumire_rol ORDER BY articole_publicate DESC";
    $d=q(DB2,$s);$tot=array_sum(array_column($d,'articole_publicate'));$med=$d?round($tot/count($d),1):0;$top=$d[0]['autor']??'N/A';$topv=$d[0]['articole_publicate']??0;
    $kpi="Cel mai productiv redactor: '$top' cu $topv articole publicate in gestiune_continut.articole. Media echipei: $med articole/autor. Aceasta analiza cross-node (JOIN intre gestiune_utilizatori.utilizatori si gestiune_continut.articole pe campul id_autor) revela distributia reala a muncii editoriale. ".($med<2?"Media sub 2 articole/autor indica un dezechilibru: fie un singur redactor produce tot continutul, fie restul echipei are blocaje neidentificate. Verificati tabelul articole pentru drafturi vechi atribuite fiecarui autor.":"Echipa produce constant. O diferenta de 3x sau mai mare intre cel mai productiv si cel mai putin productiv autor indica o problema de motivatie sau capacitate care necesita interventie manageriala.");
    resp('O11 - Clasamentul autorilor dupa productivitate','DB1 x DB2','JOIN cross-node: contributia fiecarui autor la continutul publicat.',$s,$d,
        bar($d,'autor','articole_publicate','#7c6fcd'),$kpi);
    exit;
}
if($ob==='12'){
    verifica_rol(['administrator','redactor']);
    $s="SELECT a.titlu,COUNT(DISTINCT cm.id_comentariu) AS nr_comentarii,ROUND(AVG(ev.nota),2) AS medie_nota,a.numar_vizualizari FROM ".DB2.".articole a LEFT JOIN ".DB3.".comentarii cm ON cm.id_articol=a.id_articol AND cm.aprobat=1 LEFT JOIN ".DB3.".evaluari ev ON ev.id_articol=a.id_articol WHERE a.status='publicat' GROUP BY a.id_articol,a.titlu,a.numar_vizualizari ORDER BY nr_comentarii DESC,medie_nota DESC";
    $d=q(DB2,$s);$totViz=array_sum(array_column($d,'numar_vizualizari'));$totCom=array_sum(array_column($d,'nr_comentarii'));$eng=$totViz>0?round($totCom/$totViz*100,1):0;
    $kpi="Rata de engagement calculata pe datele cross-node DB2xDB3: {$eng}% (comentarii aprobate din gestiune_interactiuni raportat la vizualizari totale). ".($eng<2?"Sub 2% engagement: pentru fiecare 100 de cititori care acceseaza un articol, mai putin de 2 lasa un comentariu. Publicul portalului consuma continut pasiv. Adaugarea unei intrebari directe la finalul fiecarui articol (editabila in campul continut din gestiune_continut.articole) poate dubla rata de comentarii.":"Engagement peste 2%. Articolele cu note mari si comentarii active din gestiune_interactiuni sunt pilonii reputatiei portalului si trebuie folosite ca model de referinta pentru productia editoriala.");
    resp('O12 - Articole populare: comentarii si evaluare medie','DB2 x DB3','JOIN cross-node dublu: engagement real versus trafic pasiv.',$s,$d,
        ['tip'=>'bar','etichete'=>array_column($d,'titlu'),'seturi'=>[['label'=>'Comentarii','valori'=>array_map('intval',array_column($d,'nr_comentarii')),'culoare'=>'#7c6fcd'],['label'=>'Medie nota','valori'=>array_map('floatval',array_column($d,'medie_nota')),'culoare'=>'#0F6E56'],['label'=>'Vizualizari','valori'=>array_map('intval',array_column($d,'numar_vizualizari')),'culoare'=>'#993C1D']]],$kpi);
    exit;
}
if($ob==='13'){
    verifica_rol(['administrator']);
    $s="SELECT u.id_utilizator,CONCAT(u.prenume,' ',u.nume) AS utilizator,r.denumire_rol,COUNT(DISTINCT cm.id_comentariu) AS nr_comentarii,COUNT(DISTINCT ev.id_evaluare) AS nr_evaluari FROM ".DB1.".utilizatori u JOIN ".DB1.".roluri r USING(id_rol) LEFT JOIN ".DB3.".comentarii cm ON cm.id_utilizator=u.id_utilizator LEFT JOIN ".DB3.".evaluari ev ON ev.id_utilizator=u.id_utilizator GROUP BY u.id_utilizator,utilizator,r.denumire_rol HAVING COUNT(DISTINCT cm.id_comentariu)>0 OR COUNT(DISTINCT ev.id_evaluare)>0 ORDER BY (COUNT(DISTINCT cm.id_comentariu)+COUNT(DISTINCT ev.id_evaluare)) DESC";
    $d=q(DB3,$s);$n=count($d);$totAct=array_sum(array_column($d,'nr_comentarii'))+array_sum(array_column($d,'nr_evaluari'));
    $kpi="$n utilizatori au generat $totAct interactiuni totale (comentarii + evaluari) in gestiune_interactiuni. Acestia sunt cititorii care transforma portalul dintr-o biblioteca statica intr-o comunitate activa. Datele lor din gestiune_interactiuni.comentarii si gestiune_interactiuni.evaluari sunt cel mai valoros feedback editorial disponibil. Un cititor activ care evalueaza articole contribuie la calibrarea calitatii editoriale. Un cititor activ care comenteaza genereaza continut aditional care creste indexarea organica a articolului. Identificarea si fidelizarea acestor utilizatori prin notificari personalizate (tabelul notificari din gestiune_interactiuni) poate transforma portalul dintr-un site de stiri intr-o comunitate loiala de lectori.";
    resp('O13 - Utilizatorii cei mai activi ca cititori','DB1 x DB3','JOIN cross-node cu HAVING: cititorii care interactioneaza activ.',$s,$d,
        ['tip'=>'bar','etichete'=>array_column($d,'utilizator'),'seturi'=>[['label'=>'Comentarii','valori'=>array_map('intval',array_column($d,'nr_comentarii')),'culoare'=>'#0F6E56'],['label'=>'Evaluari','valori'=>array_map('intval',array_column($d,'nr_evaluari')),'culoare'=>'#BA7517']]],$kpi);
    exit;
}
if($ob==='14'){
    verifica_rol(['administrator']);
    $s="SELECT (SELECT COUNT(*) FROM ".DB1.".utilizatori) AS total_utilizatori,(SELECT COUNT(*) FROM ".DB2.".articole WHERE status='publicat') AS articole_publicate,(SELECT COUNT(*) FROM ".DB2.".articole WHERE status='draft') AS articole_draft,(SELECT COUNT(*) FROM ".DB3.".comentarii WHERE aprobat=1) AS total_comentarii,(SELECT ROUND(AVG(nota),2) FROM ".DB3.".evaluari) AS medie_evaluari,(SELECT COUNT(*) FROM ".DB3.".vizualizari) AS total_vizualizari";
    $d=q(DB1,$s);$v=$d[0]??[];
    $eng=$v['total_vizualizari']>0?round($v['total_comentarii']/$v['total_vizualizari']*100,2):0;
    $kpi="Tabloul de bord complet al portalului: {$v['total_utilizatori']} utilizatori inregistrati in gestiune_utilizatori, {$v['articole_publicate']} articole vizibile publicului in gestiune_continut, {$v['total_comentarii']} comentarii aprobate si {$v['total_vizualizari']} vizualizari totale in gestiune_interactiuni, cu o medie de {$v['medie_evaluari']}/5 stele. Rata de engagement: {$eng}%. ".($eng<1?"Sub 1%: din fiecare 100 de persoane care citesc un articol, mai putin de una lasa un comentariu. Portalul are audienta dar nu are comunitate. Implementati un mecanism de incurajare a comentariilor direct in interfata de afisare a articolelor.":"In zona sanatoasa pentru un portal in crestere. Mentinerea acestui nivel necesita raspunsuri active din partea redactiei la comentariile cititorilor.")." Articole in draft: {$v['articole_draft']}. ".($v['articole_draft']>$v['articole_publicate']?"Numarul de drafturi depaseste numarul de articole publicate: echipa produce continut dar nu il finalizeaza. Blocajul este in procesul de revizie. Administratorul trebuie sa intervina pentru a debloca pipeline-ul editorial.":"Raportul draft/publicat este echilibrat: continutul creat ajunge la audienta in ritm constant.");
    resp('O14 - Raport KPI complet: Dashboard decizional','DB1 x DB2 x DB3','Subinterogari scalare pe toate 3 noduri.',$s,$d,['tip'=>'kpi','valori'=>$v],$kpi);
    exit;
}
if($ob==='rol'){
    verifica_rol(['administrator']);
    $f=$rs?"AND r.denumire_rol='$rs'":'';
    $s="SELECT u.id_utilizator,CONCAT(u.prenume,' ',u.nume) AS utilizator,r.denumire_rol AS rol,u.email,u.activ,(SELECT COUNT(*) FROM ".DB2.".articole a WHERE a.id_autor=u.id_utilizator AND a.status='publicat') AS articole_publicate,(SELECT COUNT(*) FROM ".DB3.".comentarii c WHERE c.id_utilizator=u.id_utilizator AND c.aprobat=1) AS comentarii_date FROM utilizatori u JOIN roluri r USING(id_rol) WHERE u.activ=1 $f ORDER BY r.denumire_rol,articole_publicate DESC";
    $d=q(DB1,$s);$br=[];foreach($d as $row)$br[$row['rol']]=($br[$row['rol']]??0)+1;
    $kpi=($rs?"Vedere filtrata pentru rolul '$rs': ".count($d)." utilizatori activi.":"Toti ".count($d)." utilizatori activi ai portalului.")." Aceasta interogare simuleaza accesul autorizat diferentiat: un redactor din gestiune_utilizatori care acceseaza gestiune_continut vede doar articolele sale (filtrat pe id_autor), nu si articolele colegilor in draft. Un cititor poate lasa comentarii si evaluari dar nu poate aproba comentariile altora (permisiunea add_comment=1, delete_comment=0 in tabelul permisiuni). Aceasta separare garanteaza ca datele personale ale utilizatorilor (email, parola_hash din gestiune_utilizatori) nu sunt accesibile prin functionalitatile de continut sau interactiune ale portalului.";
    resp('Filtrare pe rol - acces autorizat','DB1 x DB2 x DB3','Vizibilitate per rol cu subinterogari scalare cross-node.',$s,$d,
        ['tip'=>'pie','etichete'=>array_keys($br),'valori'=>array_values($br),'culori'=>['#7c6fcd','#0F6E56','#993C1D']],$kpi);
    exit;
}
if($ob==='metadata'){
    verifica_rol(['administrator']);
    $dbs="'".DB1."','".DB2."','".DB3."'";
    $s="SELECT TABLE_SCHEMA AS baza,TABLE_NAME AS tabel,TABLE_ROWS AS randuri,ROUND(DATA_LENGTH/1024,2) AS kb,JSON_OBJECT('baza',TABLE_SCHEMA,'tabel',TABLE_NAME,'randuri',TABLE_ROWS,'motor',ENGINE,'kb',ROUND(DATA_LENGTH/1024,2)) AS metadata_json FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA IN ($dbs) AND TABLE_TYPE='BASE TABLE' ORDER BY TABLE_SCHEMA,TABLE_NAME";
    $d=q(DB1,$s);$totTbl=count($d);$totKb=round(array_sum(array_column($d,'kb')),2);$goale=count(array_filter($d,function($r){return $r['randuri']==0;}));
    $ch=bar($d,'tabel','randuri','#7c6fcd');$ch['etichete']=array_column($d,'tabel');
    $kpi="$totTbl tabele distribuite pe 3 noduri, ocupand {$totKb} KB total. $goale tabele cu 0 randuri: structuri definite in schema BD dar neutilizate inca (sesiuni, versiuni_articole, notificari). Prezenta lor confirma ca arhitectura a anticipat functionalitati viitoare: versionarea articolelor, sistemul de notificari push si managementul sesiunilor autentificate. Monitorizarea dimensiunii tabelului gestiune_interactiuni.vizualizari este critica pe termen lung: acesta va creste cel mai rapid si va necesita strategii de arhivare. Tabelul gestiune_interactiuni.statistici_zilnice exista tocmai pentru a pre-agrega aceste date si a evita query-uri lente pe milioane de randuri brute.";
    resp('Metadate - INFORMATION_SCHEMA cu JSON_OBJECT','INFORMATION_SCHEMA','Structura interna a celor 3 noduri extrasa cu JSON_OBJECT nativ.',$s,$d,$ch,$kpi);
    exit;
}
if($ob==='json'){
    verifica_rol(['administrator']);
    $dbs="'".DB1."','".DB2."','".DB3."'";
    $s="SELECT TABLE_SCHEMA AS baza,TABLE_NAME AS tabel,GROUP_CONCAT(JSON_OBJECT('coloana',COLUMN_NAME,'tip',COLUMN_TYPE,'cheie',COLUMN_KEY,'null',IS_NULLABLE) ORDER BY ORDINAL_POSITION SEPARATOR ', ') AS coloane_json FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA IN ($dbs) GROUP BY TABLE_SCHEMA,TABLE_NAME ORDER BY TABLE_SCHEMA,TABLE_NAME";
    $d=q(DB1,$s);$totCol=0;foreach($d as $row)$totCol+=substr_count($row['coloane_json'],'coloana');
    $kpi="Schema celor 3 noduri contine $totCol coloane totale serializate ca JSON direct din INFORMATION_SCHEMA. Aceasta nu este o simpla lista de campuri: este documentatia vie a arhitecturii portalului, incluzand cheia fiecarui tabel (COLUMN_KEY), tipul de date exact (COLUMN_TYPE) si daca campul accepta valori nule (IS_NULLABLE). Exportul automat JSON al schemei are valoare practica directa: la orice modificare structurala a bazei, un SELECT pe aceasta interogare genereaza instantaneu documentatia actualizata, garantand ca documentatia BD si realitatea din MySQL sunt mereu sincronizate.";
    resp('Date JSON - GROUP_CONCAT + JSON_OBJECT nativ MySQL','INFORMATION_SCHEMA','',$s,$d,null,$kpi);
    exit;
}
if($ob==='subquery'){
    verifica_rol(['administrator','redactor']);
    $s="SELECT a.id_articol,a.titlu,a.status,ROUND((SELECT AVG(nota) FROM ".DB3.".evaluari WHERE id_articol=a.id_articol),2) AS medie_articol,ROUND((SELECT AVG(nota) FROM ".DB3.".evaluari),2) AS medie_globala FROM ".DB2.".articole a WHERE a.status='publicat' AND (SELECT AVG(nota) FROM ".DB3.".evaluari ev WHERE ev.id_articol=a.id_articol)>(SELECT AVG(nota) FROM ".DB3.".evaluari) ORDER BY medie_articol DESC";
    $d=q(DB2,$s);$n=count($d);$med=$d?round(array_sum(array_column($d,'medie_articol'))/count($d),2):0;$glob=$d[0]['medie_globala']??0;
    $kpi="$n articole din gestiune_continut.articole depasesc media globala de {$glob}/5 stele calculata pe toate evaluarile din gestiune_interactiuni.evaluari, cu o medie proprie de {$med}/5. Tehnica subquery-ului scalar este eficienta: MySQL calculeaza media inline pentru fiecare articol si o compara cu media globala in acelasi pas. Continutul din rezultat reprezinta activele cele mai valoroase ale portalului, articole pentru care cititorii au votat activ si consecvent peste medie. Administratorul ar trebui sa le promoveze pe pagina principala a portalului si sa le foloseasca ca material de referinta pentru briefing-urile editoriale.";
    resp('Subinterogari scalare - articole peste media globala','DB2 x DB3','',$s,$d,null,$kpi);
    exit;
}
if($ob==='adauga_articol'){
    verifica_rol(['administrator','redactor']);
    header('Content-Type: application/json; charset=utf-8');
    if($_SERVER['REQUEST_METHOD']!=='POST'){echo json_encode(['eroare'=>'Metoda invalida']);exit;}
    $titlu=trim($_POST['titlu']??'');$cont=trim($_POST['continut']??'');
    $rez=trim($_POST['rezumat']??'');$ida=intval($_POST['id_autor']??0);
    $idc=intval($_POST['id_categorie']??0);
    $st=in_array($_POST['status']??'',['draft','publicat','arhivat'])?$_POST['status']:'draft';
    if(!$titlu||!$cont||!$ida||!$idc){echo json_encode(['eroare'=>'Campuri obligatorii lipsa']);exit;}
    $c=db(DB2);
    $sl=substr(trim(preg_replace('/[^a-z0-9]+/','-',strtolower($titlu)),'-'),0,200).'-'.time();
    $pub=$st==='publicat'?"'".date('Y-m-d H:i:s')."'":'NULL';
    $res=$c->query("INSERT INTO articole(titlu,slug,continut,rezumat,id_autor,id_categorie,status,data_publicarii) VALUES('".$c->real_escape_string($titlu)."','".$c->real_escape_string($sl)."','".$c->real_escape_string($cont)."','".$c->real_escape_string($rez)."',$ida,$idc,'$st',$pub)");
    echo json_encode($res?['ok'=>true,'id'=>$c->insert_id,'mesaj'=>'Articol adaugat cu succes.']:['eroare'=>$c->error]);
    exit;
}
out(['eroare'=>'Obiectiv necunoscut: '.$ob]);
