<?php

class Student {

    public $nume;
    public $gen;
    public $anStudii;
    public $grupa;
    public $medie;

    public function __construct($nume, $gen, $anStudii, $grupa, $medie = 0.0) {
        $this->nume     = $nume;
        $this->gen      = $gen;
        $this->anStudii = $anStudii;
        $this->grupa    = $grupa;
        $this->medie    = $medie;
    }

    public function invata() {
        echo $this->nume . " invata...\n";
    }

    public function doarme() {
        echo $this->nume . " doarme...\n";
    }

    public function maninca() {
        echo $this->nume . " maninca...\n";
    }

    public function afiseaza() {
        echo "Nume: "         . $this->nume     . "\n";
        echo "Gen: "          . $this->gen      . "\n";
        echo "An de studii: " . $this->anStudii . "\n";
        echo "Grupa: "        . $this->grupa    . "\n";
        echo "Media: "        . $this->medie    . "\n";
    }
}

// --- Utilizare ---
$st = new Student("Maria Ionescu", "fata", 2, "TI-221", 9.50);
$st->afiseaza();
echo "\n";
$st->invata();
$st->maninca();
$st->doarme();

?>
