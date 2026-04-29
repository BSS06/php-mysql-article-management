<?php
class Lector {
    private $nume;
    private $virsta;
    private $titlu;

    public function setNume($nume) { $this->nume = $nume; }
    public function getNume() { return $this->nume; }
    public function setVirsta($virsta) { $this->virsta = $virsta; }
    public function getVirsta() { return $this->virsta; }
    public function setTitlu($titlu) { $this->titlu = $titlu; }
    public function getTitlu() { return $this->titlu; }
}
class Grupa extends Lector {
    private $Nume_grupa;
    private $Numar_studenti;
    public function setNume_grupa($val) { $this->Nume_grupa = $val; }
    public function getNume_grupa() { return $this->Nume_grupa; }
    public function setNumar_studenti($val) { $this->Numar_studenti = $val; }
    public function getNumar_studenti() { return $this->Numar_studenti; }
}
$lector1 = new Lector();
$lector1->setNume("Ion");
$lector1->setVirsta(25);
$lector1->setTitlu("profesor");
$lector2 = new Lector();
$lector2->setNume("Victor");
$lector2->setVirsta(26);
$lector2->setTitlu("conf.univ");
echo "Lector: " . $lector1->getNume() . ", Varsta: " . $lector1->getVirsta() . ", Titlu: " . $lector1->getTitlu() . "\n";
echo "Lector: " . $lector2->getNume() . ", Varsta: " . $lector2->getVirsta() . ", Titlu: " . $lector2->getTitlu() . "\n";
echo "Suma varstelor lui " . $lector1->getNume() . " si " . $lector2->getNume() . ": " . ($lector1->getVirsta() + $lector2->getVirsta()) . "\n";
echo "\n";
$grupa1 = new Grupa();
$grupa1->setNume("Andrei Popa");
$grupa1->setVirsta(40);
$grupa1->setTitlu("dr.");
$grupa1->setNume_grupa("TI-211");
$grupa1->setNumar_studenti(25);
echo "Lector grupa: " . $grupa1->getNume() . ", Titlu: " . $grupa1->getTitlu() . "\n";
echo "Grupa: " . $grupa1->getNume_grupa() . ", Studenti: " . $grupa1->getNumar_studenti() . "\n";