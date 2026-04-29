<?php
class Utilizator {
    protected $Nume;
    protected $Virsta;
    public function setNume($Nume) { $this->Nume = $Nume; }
    public function getNume() { return $this->Nume; }
    public function setVirsta($Virsta) { $this->Virsta = $Virsta; }
    public function getVirsta() { return $this->Virsta; }
}
class Angajat extends Utilizator {
    private $Salariu;
    public function setSalariu($Salariu) { $this->Salariu = $Salariu; }
    public function getSalariu() { return $this->Salariu; }
}
class Auto_drept extends Angajat {
    private $exp_driver;
    private $categorie_driver;
    public function setExpDriver($exp_driver) { $this->exp_driver = $exp_driver; }
    public function getExpDriver() { return $this->exp_driver; }
    public function setCategorieDriver($categorie_driver) { $this->categorie_driver = $categorie_driver; }
    public function getCategorieDriver() { return $this->categorie_driver; }
}
$ion = new Angajat();
$ion->setNume("Ion");
$ion->setVirsta(25);
$ion->setSalariu(1000);
$victor = new Angajat();
$victor->setNume("Victor");
$victor->setVirsta(26);
$victor->setSalariu(2000);
echo "Angajat: " . $ion->getNume() . ", Salariu: " . $ion->getSalariu() . "\n";
echo "Angajat: " . $victor->getNume() . ", Salariu: " . $victor->getSalariu() . "\n";
echo "Suma salariilor Ion si Victor: " . ($ion->getSalariu() + $victor->getSalariu()) . "\n";
echo "\n";
$sofer = new Auto_drept();
$sofer->setNume("Petru");
$sofer->setVirsta(35);
$sofer->setSalariu(3000);
$sofer->setExpDriver(10);
$sofer->setCategorieDriver("B");
echo "Sofer: " . $sofer->getNume() . ", Varsta: " . $sofer->getVirsta() . ", Salariu: " . $sofer->getSalariu() . "\n";
echo "Experienta: " . $sofer->getExpDriver() . " ani, Categorie: " . $sofer->getCategorieDriver() . "\n";