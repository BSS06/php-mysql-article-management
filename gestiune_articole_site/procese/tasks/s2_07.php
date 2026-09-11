<?php
class Angajat {
    private $nume; private $virsta; private $salariu;
    public function setNume($nume) { $this->nume = $nume; }
    public function getNume() { return $this->nume; }
    public function setVirsta($virsta) { $this->virsta = $virsta; }
    public function getVirsta() { return $this->virsta; }
    public function setSalariu($salariu) { $this->salariu = $salariu; }
    public function getSalariu() { return $this->salariu; }
}
$u1 = procese_pick_user($ctx, 0); $u2 = procese_pick_user($ctx, 1);
$ion = new Angajat(); $ion->setNume($u1['nume_complet']); $ion->setVirsta(25); $ion->setSalariu(1000);
$victor = new Angajat(); $victor->setNume($u2['nume_complet']); $victor->setVirsta(26); $victor->setSalariu(2000);
echo "Salariul lui " . $ion->getNume() . ": " . $ion->getSalariu() . "\n";
echo "Salariul lui " . $victor->getNume() . ": " . $victor->getSalariu() . "\n";
echo "Suma varstelor lui " . $ion->getNume() . " si " . $victor->getNume() . ": " . ($ion->getVirsta() + $victor->getVirsta()) . "\n";
