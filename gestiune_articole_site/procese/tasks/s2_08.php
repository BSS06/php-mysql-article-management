<?php
class Angajat {
    private $nume; private $virsta; private $salariu;
    public function setNume($nume) { $this->nume = $nume; }
    public function getNume() { return $this->nume; }
    public function setSalariu($salariu) { $this->salariu = $salariu; }
    public function getSalariu() { return $this->salariu; }
    public function getVirsta() { return $this->virsta; }
    private function checkAge($virsta) { return ($virsta >= 1 && $virsta <= 100); }
    public function setVirsta($virsta) {
        if ($this->checkAge($virsta)) $this->virsta = $virsta; else echo "Varsta {$virsta} nu este corecta. Varsta nu a fost modificata.\n";
    }
}
$ion = new Angajat(); $ion->setNume('Editor valid'); $ion->setVirsta(25); $ion->setSalariu(1000);
$victor = new Angajat(); $victor->setNume('Editor test'); $victor->setVirsta(26); $victor->setSalariu(2000); $victor->setVirsta(150);
echo "Salariul lui " . $ion->getNume() . ": " . $ion->getSalariu() . "\n";
echo "Salariul lui " . $victor->getNume() . ": " . $victor->getSalariu() . "\n";
echo "Suma varstelor lui " . $ion->getNume() . " si " . $victor->getNume() . ": " . ($ion->getVirsta() + $victor->getVirsta()) . "\n";
