<?php
class BD {
    public $nume_bd;
    public $tip_bd;
    public $host;
    public $utilizator;
    public $parola;
    protected $conexiune;
    public function __construct($nume_bd, $tip_bd, $host, $utilizator, $parola) {
        $this->nume_bd = $nume_bd;
        $this->tip_bd = $tip_bd;
        $this->host = $host;
        $this->utilizator = $utilizator;
        $this->parola = $parola;
        $this->conexiune = null;
    }
    public function conecteaza() {
        try {
            $this->conexiune = new PDO("sqlite:{$this->nume_bd}.db");
            $this->conexiune->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conectat la BD: {$this->nume_bd}\n";
        } catch (PDOException $e) {
            echo "Eroare conexiune: " . $e->getMessage() . "\n";
        }
    }
    public function creeazaTabel($sql) {
        if ($this->conexiune) {
            $this->conexiune->exec($sql);
            echo "Tabel creat cu succes.\n";
        }
    }
    public function inchideConexiunea() {
        $this->conexiune = null;
        echo "Conexiunea inchisa.\n";
    }
    public function getConexiune() {
        return $this->conexiune;
    }
}
class TABELE extends BD {
    public $nume_tabel;
    public $numar_coloane;
    public $tip_motor;
    public function __construct($nume_bd, $tip_bd, $host, $utilizator, $parola, $nume_tabel, $numar_coloane, $tip_motor) {
        parent::__construct($nume_bd, $tip_bd, $host, $utilizator, $parola);
        $this->nume_tabel = $nume_tabel;
        $this->numar_coloane = $numar_coloane;
        $this->tip_motor = $tip_motor;
    }
    public function afiseazaInfoTabel() {
        echo "Tabel: {$this->nume_tabel}, Coloane: {$this->numar_coloane}, Motor: {$this->tip_motor}\n";
    }
    public function insereazaDate($sql) {
        if ($this->getConexiune()) {
            $this->getConexiune()->exec($sql);
            echo "Date inserate in tabelul {$this->nume_tabel}.\n";
        }
    }
}
$bd1 = new BD("biblioteca", "SQLite", "localhost", "admin", "parola123");
$bd1->conecteaza();
$bd1->creeazaTabel("CREATE TABLE IF NOT EXISTS carti (id INTEGER PRIMARY KEY, titlu TEXT, autor TEXT)");
$bd1->creeazaTabel("CREATE TABLE IF NOT EXISTS autori (id INTEGER PRIMARY KEY, nume TEXT, nationalitate TEXT)");
$bd1->creeazaTabel("CREATE TABLE IF NOT EXISTS cititori (id INTEGER PRIMARY KEY, nume TEXT, email TEXT)");
$bd2 = new BD("magazin", "SQLite", "localhost", "root", "secret");
$bd2->conecteaza();
$bd2->creeazaTabel("CREATE TABLE IF NOT EXISTS produse (id INTEGER PRIMARY KEY, denumire TEXT, pret REAL)");
$bd3 = new BD("scoala", "SQLite", "localhost", "user", "pass");
$bd3->conecteaza();
$bd3->creeazaTabel("CREATE TABLE IF NOT EXISTS elevi (id INTEGER PRIMARY KEY, nume TEXT, clasa TEXT)");
echo "\n";
$tabel1 = new TABELE("biblioteca", "SQLite", "localhost", "admin", "parola123", "carti", 3, "InnoDB");
$tabel1->conecteaza();
$tabel1->insereazaDate("INSERT OR IGNORE INTO carti (id, titlu, autor) VALUES (1, 'Miorita', 'Anonim')");
$tabel1->afiseazaInfoTabel();
$tabel2 = new TABELE("biblioteca", "SQLite", "localhost", "admin", "parola123", "autori", 3, "InnoDB");
$tabel2->conecteaza();
$tabel2->insereazaDate("INSERT OR IGNORE INTO autori (id, nume, nationalitate) VALUES (1, 'Eminescu', 'Roman')");
$tabel2->afiseazaInfoTabel();
$tabel3 = new TABELE("biblioteca", "SQLite", "localhost", "admin", "parola123", "cititori", 3, "InnoDB");
$tabel3->conecteaza();
$tabel3->insereazaDate("INSERT OR IGNORE INTO cititori (id, nume, email) VALUES (1, 'Ion Popescu', 'ion@mail.com')");
$tabel3->afiseazaInfoTabel();
$bd1->inchideConexiunea();
$bd2->inchideConexiunea();
$bd3->inchideConexiunea();