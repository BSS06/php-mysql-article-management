<?php
class Flash {
    public $capacitate;
    public $producator;
    public $viteza_citire;
    public $viteza_scriere;
    public $tip_interfata;
    public function __construct($capacitate, $producator, $viteza_citire, $viteza_scriere, $tip_interfata) {
        $this->capacitate = $capacitate;
        $this->producator = $producator;
        $this->viteza_citire = $viteza_citire;
        $this->viteza_scriere = $viteza_scriere;
        $this->tip_interfata = $tip_interfata;
    }
    public function afiseazaInfo() {
        echo "Flash: {$this->producator}, {$this->capacitate}GB, Interfata: {$this->tip_interfata}\n";
    }
    public function calculeazaTimp($marime_fisier) {
        $timp = $marime_fisier / $this->viteza_scriere;
        echo "Timp scriere {$marime_fisier}MB: " . round($timp, 2) . " secunde\n";
    }
    public function afiseazaViteze() {
        echo "Citire: {$this->viteza_citire}MB/s, Scriere: {$this->viteza_scriere}MB/s\n";
    }
}
class Mapa_de_lucru extends Flash {
    public $nume_mapa;
    public $numar_fisiere;
    public $data_creare;
    public function __construct($capacitate, $producator, $viteza_citire, $viteza_scriere, $tip_interfata, $nume_mapa, $numar_fisiere, $data_creare) {
        parent::__construct($capacitate, $producator, $viteza_citire, $viteza_scriere, $tip_interfata);
        $this->nume_mapa = $nume_mapa;
        $this->numar_fisiere = $numar_fisiere;
        $this->data_creare = $data_creare;
    }
    public function afiseazaMapa() {
        echo "Mapa: {$this->nume_mapa}, Fisiere: {$this->numar_fisiere}, Data: {$this->data_creare}\n";
    }
    public function adaugaFisier() {
        $this->numar_fisiere++;
        echo "Fisier adaugat in '{$this->nume_mapa}'. Total fisiere: {$this->numar_fisiere}\n";
    }
}
$flash1 = new Flash(64, "Kingston", 100, 50, "USB 3.0");
$flash2 = new Flash(128, "SanDisk", 150, 80, "USB 3.1");
$flash3 = new Flash(32, "Samsung", 90, 45, "USB 2.0");
$flash1->afiseazaInfo();
$flash2->afiseazaInfo();
$flash3->afiseazaInfo();
$flash1->calculeazaTimp(500);
$flash2->afiseazaViteze();
echo "\n";
$mapa1 = new Mapa_de_lucru(64, "Kingston", 100, 50, "USB 3.0", "Documente", 15, "2024-01-10");
$mapa2 = new Mapa_de_lucru(128, "SanDisk", 150, 80, "USB 3.1", "Proiecte", 30, "2024-03-05");
$mapa3 = new Mapa_de_lucru(32, "Samsung", 90, 45, "USB 2.0", "Backup", 5, "2024-05-01");
$mapa1->afiseazaMapa();
$mapa2->afiseazaMapa();
$mapa3->afiseazaMapa();
$mapa1->adaugaFisier();