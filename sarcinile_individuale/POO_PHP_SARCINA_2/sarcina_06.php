<?php
class Mama {
    public $nume;
    public $virsta;
    public $profesie;
    public $nationalitate;
    public $stare_civila;
    public function __construct($nume, $virsta, $profesie, $nationalitate, $stare_civila) {
        $this->nume = $nume;
        $this->virsta = $virsta;
        $this->profesie = $profesie;
        $this->nationalitate = $nationalitate;
        $this->stare_civila = $stare_civila;
    }
    public function afiseazaInfo() {
        echo "Mama: {$this->nume}, Varsta: {$this->virsta}, Profesie: {$this->profesie}\n";
    }
    public function lucreaza() {
        echo "{$this->nume} lucreaza ca {$this->profesie}.\n";
    }
    public function afiseazaStareCivila() {
        echo "{$this->nume} este {$this->stare_civila}.\n";
    }
}
class Copil extends Mama {
    public $scoala;
    public $clasa;
    public $hobby;
    public function __construct($nume, $virsta, $profesie, $nationalitate, $stare_civila, $scoala, $clasa, $hobby) {
        parent::__construct($nume, $virsta, $profesie, $nationalitate, $stare_civila);
        $this->scoala = $scoala;
        $this->clasa = $clasa;
        $this->hobby = $hobby;
    }
    public function afiseazaCopil() {
        echo "Copil: {$this->nume}, Scoala: {$this->scoala}, Clasa: {$this->clasa}\n";
    }
    public function afiseazaHobby() {
        echo "{$this->nume} are hobby: {$this->hobby}\n";
    }
}
$mama1 = new Mama("Elena", 35, "medic", "Moldoveanca", "casatorita");
$mama2 = new Mama("Ana", 40, "profesoara", "Romanca", "casatorita");
$mama3 = new Mama("Maria", 30, "economista", "Moldoveanca", "necasatorita");
$mama1->afiseazaInfo();
$mama2->afiseazaInfo();
$mama3->afiseazaInfo();
$mama1->lucreaza();
$mama3->afiseazaStareCivila();
echo "\n";
$copil1 = new Copil("Andrei", 10, "elev", "Moldovean", "necasatorit", "Liceul nr.1", "a 4-a", "fotbal");
$copil2 = new Copil("Maria", 12, "eleva", "Moldoveanca", "necasatorita", "Liceul Teoretic", "a 6-a", "desen");
$copil3 = new Copil("Ion", 8, "elev", "Moldovean", "necasatorit", "Scoala nr.5", "a 2-a", "muzica");
$copil1->afiseazaCopil();
$copil2->afiseazaCopil();
$copil3->afiseazaCopil();
$copil1->afiseazaHobby();