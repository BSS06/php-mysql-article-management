<?php

class Trapez {

    private $x1, $y1;
    private $x2, $y2;
    private $x3, $y3;
    private $x4, $y4;

    public function __construct($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) {
        $this->x1 = $x1; $this->y1 = $y1;
        $this->x2 = $x2; $this->y2 = $y2;
        $this->x3 = $x3; $this->y3 = $y3;
        $this->x4 = $x4; $this->y4 = $y4;
    }

    private function distanta($ax, $ay, $bx, $by) {
        return sqrt(pow($bx - $ax, 2) + pow($by - $ay, 2));
    }

    public function getLaturaAB() {
        return $this->distanta($this->x1, $this->y1, $this->x2, $this->y2);
    }

    public function getLaturaBC() {
        return $this->distanta($this->x2, $this->y2, $this->x3, $this->y3);
    }

    public function getLaturaCD() {
        return $this->distanta($this->x3, $this->y3, $this->x4, $this->y4);
    }

    public function getLaturaDA() {
        return $this->distanta($this->x4, $this->y4, $this->x1, $this->y1);
    }

    public function getPerimetru() {
        return $this->getLaturaAB() + $this->getLaturaBC()
             + $this->getLaturaCD() + $this->getLaturaDA();
    }

    public function getSuprafata() {
        $b1 = $this->getLaturaAB();
        $b2 = $this->getLaturaCD();
        $A  = $this->y2 - $this->y1;
        $B  = $this->x1 - $this->x2;
        $C  = $this->x2 * $this->y1 - $this->x1 * $this->y2;
        $h  = abs($A * $this->x3 + $B * $this->y3 + $C) / sqrt($A * $A + $B * $B);
        return ($b1 + $b2) * $h / 2;
    }

    public function esteIsoscel() {
        return round($this->getLaturaBC(), 6) === round($this->getLaturaDA(), 6);
    }

    public function afiseaza() {
        echo "Latura AB: " . round($this->getLaturaAB(), 4) . "\n";
        echo "Latura BC: " . round($this->getLaturaBC(), 4) . "\n";
        echo "Latura CD: " . round($this->getLaturaCD(), 4) . "\n";
        echo "Latura DA: " . round($this->getLaturaDA(), 4) . "\n";
        echo "Perimetru: " . round($this->getPerimetru(), 4) . "\n";
        echo "Suprafata: " . round($this->getSuprafata(), 4) . "\n";
        echo "Este isoscel: " . ($this->esteIsoscel() ? "DA" : "NU") . "\n";
    }
}

// --- Utilizare ---
$t = new Trapez(0, 0, 6, 0, 5, 4, 1, 4);
$t->afiseaza();

?>
