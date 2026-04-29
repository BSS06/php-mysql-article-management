<?php

class Librarie {
    public $nume;
    public $adresa;
    public $numar_carti;
    public $an_fondare;
    public $tip;

    public function __construct($nume, $adresa, $numar_carti, $an_fondare, $tip) {
        $this->nume = $nume;
        $this->adresa = $adresa;
        $this->numar_carti = $numar_carti;
        $this->an_fondare = $an_fondare;
        $this->tip = $tip;
    }

    public function afiseazaInfo() {
        echo "Librarie: {$this->nume}, Adresa: {$this->adresa}, Carti: {$this->numar_carti}\n";
    }

    public function adaugaCarti($numar) {
        $this->numar_carti += $numar;
        echo "Adaugate {$numar} carti. Total: {$this->numar_carti}\n";
    }

    public function afiseazaTip() {
        echo "Tip librarie: {$this->tip}\n";
    }
}

class Sala_lectura extends Librarie {
    public $numar_locuri;
    public $program;
    public $responsabil;

    public function __construct($nume, $adresa, $numar_carti, $an_fondare, $tip, $numar_locuri, $program, $responsabil) {
        parent::__construct($nume, $adresa, $numar_carti, $an_fondare, $tip);
        $this->numar_locuri = $numar_locuri;
        $this->program = $program;
        $this->responsabil = $responsabil;
    }

    public function afiseazaSala() {
        echo "Sala lectura: {$this->nume}, Locuri: {$this->numar_locuri}, Program: {$this->program}\n";
    }

    public function verificaDisponibilitate() {
        echo "Sala '{$this->nume}' este disponibila. Responsabil: {$this->responsabil}\n";
    }
}

$lib1 = new Librarie("Biblioteca Nationala", "Chisinau, str. 31 August", 500000, 1832, "Publica");
$lib2 = new Librarie("Cartea Moldovei", "Chisinau, bd. Stefan cel Mare", 120000, 1990, "Privata");
$lib3 = new Librarie("Biblioteca Universitara", "Chisinau, str. Puskin", 300000, 1946, "Academica");

$lib1->afiseazaInfo();
$lib2->afiseazaInfo();
$lib3->afiseazaInfo();
$lib1->adaugaCarti(500);
$lib1->afiseazaTip();

echo "\n";

$sala1 = new Sala_lectura("Sala A", "Chisinau, str. 31 August", 10000, 1900, "Publica", 50, "08:00-20:00", "Maria Ionescu");
$sala2 = new Sala_lectura("Sala B", "Chisinau, str. Puskin", 5000, 1950, "Academica", 30, "09:00-18:00", "Ion Popa");
$sala3 = new Sala_lectura("Sala C", "Chisinau, bd. Stefan", 8000, 1960, "Publica", 40, "10:00-22:00", "Ana Rusu");

$sala1->afiseazaSala();
$sala2->afiseazaSala();
$sala3->afiseazaSala();
$sala1->verificaDisponibilitate();
