<?php
class Fractional {
    private $intreg;
    private $fractionar;
    public function __construct($intreg = 0, $fractionar = 0) {
        $this->intreg = (int)$intreg;
        $this->fractionar = (int)$fractionar;
    }
    private function laFloat() {
        $semn = ($this->intreg < 0 || $this->fractionar < 0) ? -1 : 1;
        $i = abs($this->intreg);
        $f = abs($this->fractionar);
        $cifre = $f > 0 ? strlen((string)$f) : 1;
        return $semn * ($i + $f / pow(10, $cifre));
    }
    private static function dinFloat($val) {
        $i = (int)$val;
        $f = (int)round(abs($val - $i) * 100);
        return new Fractional($i, $f);
    }
    public function aduna(Fractional $b) { return Fractional::dinFloat($this->laFloat() + $b->laFloat()); }
    public function scade(Fractional $b) { return Fractional::dinFloat($this->laFloat() - $b->laFloat()); }
    public function inmulteste(Fractional $b) { return Fractional::dinFloat($this->laFloat() * $b->laFloat()); }
    public function imparte(Fractional $b) {
        if ($b->laFloat() == 0) { echo "Eroare: impartire la zero!\n"; return null; }
        return Fractional::dinFloat($this->laFloat() / $b->laFloat());
    }
    public function compara(Fractional $b) {
        $diff = $this->laFloat() - $b->laFloat();
        if ($diff < 0) return -1;
        if ($diff > 0) return 1;
        return 0;
    }
    public function afiseaza() { echo $this->intreg . "." . str_pad(abs($this->fractionar), 2, "0", STR_PAD_LEFT); }
}
$rating = (float)($ctx['scope']['counts']['avg_rating'] ?? 0);
$comentarii = (int)($ctx['scope']['counts']['approved_comments'] ?? 0);
$viewuri = max(1, (int)($ctx['scope']['top_article']['numar_vizualizari'] ?? 1));
$engagement = round(($comentarii / $viewuri) * 10, 2);
$f1 = new Fractional((int)$rating, (int)round(($rating - (int)$rating) * 100));
$f2 = new Fractional((int)$engagement, (int)round(($engagement - (int)$engagement) * 100));
echo "f1 = "; $f1->afiseaza(); echo "\n";
echo "f2 = "; $f2->afiseaza(); echo "\n";
$suma = $f1->aduna($f2); echo "f1 + f2 = "; $suma->afiseaza(); echo "\n";
$dif = $f1->scade($f2); echo "f1 - f2 = "; $dif->afiseaza(); echo "\n";
$pro = $f1->inmulteste($f2); echo "f1 * f2 = "; $pro->afiseaza(); echo "\n";
$div = $f1->imparte($f2); echo "f1 / f2 = "; if ($div) $div->afiseaza(); echo "\n";
echo "f1 compara f2: " . $f1->compara($f2) . " (1=mai mare, -1=mai mic, 0=egal)\n";
