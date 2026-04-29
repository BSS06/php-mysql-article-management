<?php
class Sesiune {
    private $date_sesiune = [];
    public function __construct() {
        $this->date_sesiune = [];
        echo "Sesiunea a fost initializata.\n";
    }
    public function seteazaVariabila($cheie, $valoare) {
        $this->date_sesiune[$cheie] = $valoare;
    }
    public function obtineVariabila($cheie) {
        return isset($this->date_sesiune[$cheie]) ? $this->date_sesiune[$cheie] : null;
    }
    public function stergeVariabila($cheie) {
        if (isset($this->date_sesiune[$cheie])) {
            unset($this->date_sesiune[$cheie]);
        }
    }
    public function verificaVariabila($cheie) {
        return isset($this->date_sesiune[$cheie]);
    }
}
class Flash {
    private $sesiune;
    public function __construct(Sesiune $sesiune) {
        $this->sesiune = $sesiune;
    }
    public function setMessage($mesaj) {
        $this->sesiune->seteazaVariabila("flash_message", $mesaj);
    }
    public function getMessage() {
        $mesaj = $this->sesiune->obtineVariabila("flash_message");
        $this->sesiune->stergeVariabila("flash_message");
        return $mesaj;
    }
}
$sesiunea_1 = new Sesiune();
$sesiunea_1->seteazaVariabila("utilizator", "Ion");
$sesiunea_1->seteazaVariabila("rol", "admin");
echo "Variabila 'utilizator': " . $sesiunea_1->obtineVariabila("utilizator") . "\n";
echo "Variabila 'rol' exista: " . ($sesiunea_1->verificaVariabila("rol") ? "da" : "nu") . "\n";
$sesiunea_1->stergeVariabila("rol");
echo "Variabila 'rol' dupa stergere exista: " . ($sesiunea_1->verificaVariabila("rol") ? "da" : "nu") . "\n";
echo "\n";
$sesiunea_2 = new Sesiune();
$sesiunea_2->seteazaVariabila("pagina", "home");
$flash = new Flash($sesiunea_2);
$flash->setMessage("Formularul a fost trimis cu succes!");
echo "Mesaj flash: " . $flash->getMessage() . "\n";
echo "Mesaj flash dupa citire: " . ($flash->getMessage() ?? "null") . "\n";