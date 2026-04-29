<?php
class Regiune {
    public $nume;
    public $suprafata;
    public $populatie;
    public $capitala;
    public $cod_iso;
    public function __construct($nume, $suprafata, $populatie, $capitala, $cod_iso) {
        $this->nume = $nume;
        $this->suprafata = $suprafata;
        $this->populatie = $populatie;
        $this->capitala = $capitala;
        $this->cod_iso = $cod_iso;
    }
    public function afiseazaInfo() {
        echo "Regiune: {$this->nume}, Capitala: {$this->capitala}, Populatie: {$this->populatie}\n";
    }
    public function calculeazaDensitate() {
        return round($this->populatie / $this->suprafata, 2);
    }
    public function afiseazaCodIso() {
        echo "Cod ISO: {$this->cod_iso}\n";
    }
}
class Raioane extends Regiune {
    public $numar_localitati;
    public $tip_administrativ;
    public $presedinte;
    public function __construct($nume, $suprafata, $populatie, $capitala, $cod_iso, $numar_localitati, $tip_administrativ, $presedinte) {
        parent::__construct($nume, $suprafata, $populatie, $capitala, $cod_iso);
        $this->numar_localitati = $numar_localitati;
        $this->tip_administrativ = $tip_administrativ;
        $this->presedinte = $presedinte;
    }
    public function afiseazaRaion() {
        echo "Raion: {$this->nume}, Localitati: {$this->numar_localitati}, Presedinte: {$this->presedinte}\n";
    }
    public function afiseazaTipAdministrativ() {
        echo "Tip: {$this->tip_administrativ}\n";
    }
}
$reg1 = new Regiune("Nord", 12000, 450000, "Balti", "MD-N");
$reg2 = new Regiune("Centru", 10000, 800000, "Chisinau", "MD-C");
$reg3 = new Regiune("Sud", 9000, 250000, "Cahul", "MD-S");
$reg1->afiseazaInfo();
$reg2->afiseazaInfo();
$reg3->afiseazaInfo();
echo "Densitate Nord: " . $reg1->calculeazaDensitate() . " loc/km2\n";
echo "\n";
$raion1 = new Raioane("Orhei", 1831, 120000, "Orhei", "MD-OR", 76, "Raion", "Ion Vrabie");
$raion2 = new Raioane("Soroca", 1855, 95000, "Soroca", "MD-SO", 65, "Raion", "Petru Lungu");
$raion3 = new Raioane("Cahul", 1646, 75000, "Cahul", "MD-CA", 55, "Raion", "Maria Cojocaru");
$raion1->afiseazaRaion();
$raion2->afiseazaRaion();
$raion3->afiseazaRaion();
$raion1->afiseazaTipAdministrativ();