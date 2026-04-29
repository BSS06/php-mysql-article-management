<?php

class CalculatorSiruri {

    private $sir1;
    private $sir2;

    public function __construct($sir1 = "", $sir2 = "") {
        $this->sir1 = strtoupper(substr($sir1, 0, 5));
        $this->sir2 = strtoupper(substr($sir2, 0, 5));
    }

    public function esteMajuscula($simbol) {
        return ctype_upper($simbol);
    }

    public function esteMinuscula($simbol) {
        return ctype_lower($simbol);
    }

    public function concateneaza() {
        return substr($this->sir1 . $this->sir2, 0, 5);
    }

    public function join() {
        return $this->sir1 . " " . $this->sir2;
    }

    public function afiseaza() {
        echo "Sir1: " . $this->sir1 . "\n";
        echo "Sir2: " . $this->sir2 . "\n";
        $c = strlen($this->sir1) > 0 ? $this->sir1[0] : '';
        echo "Primul caracter (" . $c . ") este majuscula: "
             . ($this->esteMajuscula($c) ? "DA" : "NU") . "\n";
        echo "Concatenare: " . $this->concateneaza() . "\n";
        echo "Join: "        . $this->join()          . "\n";
    }
}

// --- Utilizare ---
$cs = new CalculatorSiruri("Hello", "World");
$cs->afiseaza();

?>
