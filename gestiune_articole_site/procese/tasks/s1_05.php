<?php
class Triunghi {
    private $x1, $y1, $x2, $y2, $x3, $y3;
    public function __construct($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->x1 = $x1; $this->y1 = $y1; $this->x2 = $x2; $this->y2 = $y2; $this->x3 = $x3; $this->y3 = $y3;
    }
    private function distanta($ax, $ay, $bx, $by) { return sqrt(pow($bx - $ax, 2) + pow($by - $ay, 2)); }
    public function getLaturaAB() { return $this->distanta($this->x1, $this->y1, $this->x2, $this->y2); }
    public function getLaturaBC() { return $this->distanta($this->x2, $this->y2, $this->x3, $this->y3); }
    public function getLaturaCA() { return $this->distanta($this->x3, $this->y3, $this->x1, $this->y1); }
    public function getPerimetru() { return $this->getLaturaAB() + $this->getLaturaBC() + $this->getLaturaCA(); }
    public function getSuprafata() {
        $a = $this->getLaturaAB(); $b = $this->getLaturaBC(); $c = $this->getLaturaCA();
        $s = ($a + $b + $c) / 2; return sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));
    }
    public function esteIsoscel() {
        $ab = round($this->getLaturaAB(), 6); $bc = round($this->getLaturaBC(), 6); $ca = round($this->getLaturaCA(), 6);
        return ($ab === $bc) || ($bc === $ca) || ($ab === $ca);
    }
    public function afiseaza() {
        echo "Latura AB: " . round($this->getLaturaAB(), 4) . "\n";
        echo "Latura BC: " . round($this->getLaturaBC(), 4) . "\n";
        echo "Latura CA: " . round($this->getLaturaCA(), 4) . "\n";
        echo "Perimetru: " . round($this->getPerimetru(), 4) . "\n";
        echo "Suprafata: " . round($this->getSuprafata(), 4) . "\n";
        echo "Este isoscel: " . ($this->esteIsoscel() ? "DA" : "NU") . "\n";
    }
}
$views = max(3, (int)($ctx['scope']['top_article']['numar_vizualizari'] ?? 3));
$comments = max(2, (int)($ctx['scope']['counts']['approved_comments'] ?? 2));
$tr = new Triunghi(0, 0, $views, 0, 2, $comments);
$tr->afiseaza();
