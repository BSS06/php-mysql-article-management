<?php
class Tara {
    protected $Nume;
    protected $Suprafata;
    public function setNume($Nume) { $this->Nume = $Nume; }
    public function getNume() { return $this->Nume; }
    public function setSuprafata($Suprafata) { $this->Suprafata = $Suprafata; }
    public function getSuprafata() { return $this->Suprafata; }
}
class Regiune extends Tara {
    private $Numar_populatie;
    public function setNumar_populatie($Numar_populatie) { $this->Numar_populatie = $Numar_populatie; }
    public function getNumar_populatie() { return $this->Numar_populatie; }
}
class Studii_superioare extends Tara {
    private $St_sup_un;
    private $St_sup_un_neterm;
    public function setSt_sup_un($val) { $this->St_sup_un = $val; }
    public function getSt_sup_un() { return $this->St_sup_un; }
    public function setSt_sup_un_neterm($val) { $this->St_sup_un_neterm = $val; }
    public function getSt_sup_un_neterm() { return $this->St_sup_un_neterm; }
}
$romani = new Regiune();
$romani->setNume("Romani");
$romani->setSuprafata(5000);
$romani->setNumar_populatie(200000);
$rusi = new Regiune();
$rusi->setNume("Rusi");
$rusi->setSuprafata(8000);
$rusi->setNumar_populatie(100000);
echo "Regiune: " . $romani->getNume() . ", Populatie: " . $romani->getNumar_populatie() . "\n";
echo "Regiune: " . $rusi->getNume() . ", Populatie: " . $rusi->getNumar_populatie() . "\n";
echo "Suma Numar_populatie Romani si Rusi: " . ($romani->getNumar_populatie() + $rusi->getNumar_populatie()) . "\n";
echo "\n";
$studii = new Studii_superioare();
$studii->setNume("Moldova");
$studii->setSuprafata(33846);
$studii->setSt_sup_un(50000);
$studii->setSt_sup_un_neterm(15000);
echo "Tara: " . $studii->getNume() . ", Suprafata: " . $studii->getSuprafata() . " km2\n";
echo "Studii universitare: " . $studii->getSt_sup_un() . ", Neterminate: " . $studii->getSt_sup_un_neterm() . "\n";