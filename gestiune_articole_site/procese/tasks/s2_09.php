<?php
class Utilizator {
    protected $Nume; protected $Virsta;
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
class Student extends Utilizator {
    private $bursa; private $curs;
    public function setBursa($bursa) { $this->bursa = $bursa; }
    public function getBursa() { return $this->bursa; }
    public function setCurs($curs) { $this->curs = $curs; }
    public function getCurs() { return $this->curs; }
}
$ion = new Angajat(); $ion->setNume('Redactor'); $ion->setVirsta(25); $ion->setSalariu(1000);
$victor = new Angajat(); $victor->setNume('Moderator'); $victor->setVirsta(26); $victor->setSalariu(2000);
echo "Angajat: " . $ion->getNume() . ", Varsta: " . $ion->getVirsta() . ", Salariu: " . $ion->getSalariu() . "\n";
echo "Angajat: " . $victor->getNume() . ", Varsta: " . $victor->getVirsta() . ", Salariu: " . $victor->getSalariu() . "\n";
echo "Suma salariilor: " . ($ion->getSalariu() + $victor->getSalariu()) . "\n";
echo "\n";
$student = new Student(); $student->setNume('Stagiar'); $student->setVirsta(20); $student->setBursa(500); $student->setCurs(2);
echo "Student: " . $student->getNume() . ", Varsta: " . $student->getVirsta() . ", Curs: " . $student->getCurs() . ", Bursa: " . $student->getBursa() . "\n";
