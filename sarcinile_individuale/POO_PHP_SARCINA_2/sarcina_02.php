<?php

class Republica_Moldova {
    public $nume;
    public $suprafata;
    public $populatie;
    public $capitala;
    public $limba_oficiala;

    public function __construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala) {
        $this->nume = $nume;
        $this->suprafata = $suprafata;
        $this->populatie = $populatie;
        $this->capitala = $capitala;
        $this->limba_oficiala = $limba_oficiala;
    }

    public function afiseazaInfo() {
        echo "Tara: {$this->nume}, Capitala: {$this->capitala}, Limba: {$this->limba_oficiala}\n";
    }

    public function calculeazaDensitate() {
        return round($this->populatie / $this->suprafata, 2);
    }

    public function afiseazaSuprafata() {
        echo "Suprafata: {$this->suprafata} km2\n";
    }
}

class Raionul extends Republica_Moldova {
    public $centru_raional;
    public $numar_localitati;
    public $cod_postal;

    public function __construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala, $centru_raional, $numar_localitati, $cod_postal) {
        parent::__construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala);
        $this->centru_raional = $centru_raional;
        $this->numar_localitati = $numar_localitati;
        $this->cod_postal = $cod_postal;
    }

    public function afiseazaRaion() {
        echo "Raion: {$this->nume}, Centru: {$this->centru_raional}, Localitati: {$this->numar_localitati}\n";
    }

    public function afiseazaCodPostal() {
        echo "Cod postal: {$this->cod_postal}\n";
    }
}

$rm1 = new Republica_Moldova("Republica Moldova", 33846, 2600000, "Chisinau", "Romana");
$rm2 = new Republica_Moldova("Romania", 238397, 19000000, "Bucuresti", "Romana");
$rm3 = new Republica_Moldova("Ucraina", 603550, 44000000, "Kiev", "Ucraineana");

$rm1->afiseazaInfo();
$rm2->afiseazaInfo();
$rm3->afiseazaInfo();
echo "Densitatea populatiei in Moldova: " . $rm1->calculeazaDensitate() . " loc/km2\n";
$rm1->afiseazaSuprafata();

echo "\n";

$raion1 = new Raionul("Orhei", 1831, 120000, "Chisinau", "Romana", "Orhei", 76, "MD-3500");
$raion2 = new Raionul("Soroca", 1855, 95000, "Chisinau", "Romana", "Soroca", 65, "MD-3000");

$raion1->afiseazaRaion();
$raion1->afiseazaCodPostal();
$raion2->afiseazaRaion();
$raion2->afiseazaCodPostal();
