<?php
class BD {
    public $nume_bd; public $tip_bd; public $host; public $utilizator; public $parola; protected $conexiune;
    public function __construct($nume_bd, $tip_bd, $host, $utilizator, $parola) {
        $this->nume_bd = $nume_bd; $this->tip_bd = $tip_bd; $this->host = $host; $this->utilizator = $utilizator; $this->parola = $parola; $this->conexiune = null;
    }
    public function conecteaza() { $this->conexiune = true; echo "Conectat la BD: {$this->nume_bd}\n"; }
    public function creeazaTabel($sql) { if ($this->conexiune) echo "Tabel creat cu succes.\n"; }
    public function inchideConexiunea() { $this->conexiune = null; echo "Conexiunea inchisa.\n"; }
    public function getConexiune() { return $this->conexiune; }
}
class TABELE extends BD {
    public $nume_tabel; public $numar_coloane; public $tip_motor;
    public function __construct($nume_bd, $tip_bd, $host, $utilizator, $parola, $nume_tabel, $numar_coloane, $tip_motor) {
        parent::__construct($nume_bd, $tip_bd, $host, $utilizator, $parola);
        $this->nume_tabel = $nume_tabel; $this->numar_coloane = $numar_coloane; $this->tip_motor = $tip_motor;
    }
    public function afiseazaInfoTabel() { echo "Tabel: {$this->nume_tabel}, Coloane: {$this->numar_coloane}, Motor: {$this->tip_motor}\n"; }
    public function insereazaDate($sql) { if ($this->getConexiune()) echo "Date inserate in tabelul {$this->nume_tabel}.\n"; }
}
$n1 = procese_pick_node($ctx,0); $n2 = procese_pick_node($ctx,1); $n3 = procese_pick_node($ctx,2);
$bd1 = new BD($n1['db_name'], 'MySQL', 'localhost', 'root', 'root');
$bd1->conecteaza(); $bd1->creeazaTabel('CREATE TABLE utilizatori (...)'); $bd1->creeazaTabel('CREATE TABLE roluri (...)'); $bd1->creeazaTabel('CREATE TABLE sesiuni (...)');
$bd2 = new BD($n2['db_name'], 'MySQL', 'localhost', 'root', 'root');
$bd2->conecteaza(); $bd2->creeazaTabel('CREATE TABLE articole (...)');
$bd3 = new BD($n3['db_name'], 'MySQL', 'localhost', 'root', 'root');
$bd3->conecteaza(); $bd3->creeazaTabel('CREATE TABLE comentarii (...)');
echo "\n";
$tabel1 = new TABELE($n2['db_name'], 'MySQL', 'localhost', 'root', 'root', 'articole', 12, 'InnoDB');
$tabel1->conecteaza(); $tabel1->insereazaDate('INSERT INTO articole (...) VALUES (...)'); $tabel1->afiseazaInfoTabel();
$tabel2 = new TABELE($n1['db_name'], 'MySQL', 'localhost', 'root', 'root', 'utilizatori', 10, 'InnoDB');
$tabel2->conecteaza(); $tabel2->insereazaDate('INSERT INTO utilizatori (...) VALUES (...)'); $tabel2->afiseazaInfoTabel();
$tabel3 = new TABELE($n3['db_name'], 'MySQL', 'localhost', 'root', 'root', 'comentarii', 7, 'InnoDB');
$tabel3->conecteaza(); $tabel3->insereazaDate('INSERT INTO comentarii (...) VALUES (...)'); $tabel3->afiseazaInfoTabel();
$bd1->inchideConexiunea(); $bd2->inchideConexiunea(); $bd3->inchideConexiunea();
