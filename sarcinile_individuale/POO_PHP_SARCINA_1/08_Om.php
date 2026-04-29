<?php

class Om {

    public $culoareaPar;
    public $inaltimea;
    public $greutatea;
    public $varsta;
    public $nume;

    public function __construct($nume, $varsta, $inaltimea, $greutatea, $culoareaPar) {
        $this->nume        = $nume;
        $this->varsta      = $varsta;
        $this->inaltimea   = $inaltimea;
        $this->greutatea   = $greutatea;
        $this->culoareaPar = $culoareaPar;
    }

    public function somn() {
        echo $this->nume . " doarme...\n";
    }

    public function mancare() {
        echo $this->nume . " mananca...\n";
    }

    public function plimbare() {
        echo $this->nume . " se plimba...\n";
    }

    public function afiseaza() {
        echo "Nume: "              . $this->nume        . "\n";
        echo "Varsta: "            . $this->varsta      . " ani\n";
        echo "Inaltimea: "         . $this->inaltimea   . " cm\n";
        echo "Greutatea: "         . $this->greutatea   . " kg\n";
        echo "Culoarea parului: "  . $this->culoareaPar . "\n";
    }
}

// --- Utilizare ---
$om = new Om("Ion Popescu", 30, 178, 75, "brunet");
$om->afiseaza();
echo "\n";
$om->mancare();
$om->plimbare();
$om->somn();

?>
