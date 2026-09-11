<?php
class Calculator {
    private $a;
    private $b;
    public function __construct($a = 0, $b = 0) { $this->a = $a; $this->b = $b; }
    public function esteIntreg($val) { return is_numeric($val) && (int)$val == $val; }
    public function aduna() {
        if (!$this->esteIntreg($this->a) || !$this->esteIntreg($this->b)) { echo "Eroare: ambele numere trebuie sa fie intregi!\n"; return null; }
        return $this->a + $this->b;
    }
    public function scade() {
        if (!$this->esteIntreg($this->a) || !$this->esteIntreg($this->b)) { echo "Eroare: ambele numere trebuie sa fie intregi!\n"; return null; }
        return $this->a - $this->b;
    }
    public function afiseaza() {
        echo "a = " . $this->a . ", b = " . $this->b . "\n";
        echo "a este intreg: " . ($this->esteIntreg($this->a) ? "DA" : "NU") . "\n";
        echo "b este intreg: " . ($this->esteIntreg($this->b) ? "DA" : "NU") . "\n";
        $suma = $this->aduna(); $dif = $this->scade();
        if ($suma !== null) echo "Suma este: " . $suma . "\n";
        if ($dif !== null) echo "Diferenta este: " . $dif . "\n";
    }
}
$calc = new Calculator((int)($ctx['scope']['counts']['published_count'] ?? 0), (int)($ctx['scope']['counts']['draft_count'] ?? 0));
$calc->afiseaza();
