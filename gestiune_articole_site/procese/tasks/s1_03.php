<?php
class Bani {
    private $lei;
    private $bani;
    public function __construct($lei = 0, $bani = 0) {
        $total = (int)$lei * 100 + (int)$bani;
        $this->lei = intdiv($total, 100);
        $this->bani = abs($total % 100);
    }
    private function laBani() { return $this->lei * 100 + $this->bani; }
    private static function dinBani($total) {
        $lei = intdiv($total, 100);
        $bani = abs($total % 100);
        return new Bani($lei, $bani);
    }
    public function aduna(Bani $b) { return Bani::dinBani($this->laBani() + $b->laBani()); }
    public function scade(Bani $b) { return Bani::dinBani($this->laBani() - $b->laBani()); }
    public function imparteLaNumar($n) {
        if ($n == 0) { echo "Eroare: impartire la zero!\n"; return null; }
        return Bani::dinBani((int)($this->laBani() / $n));
    }
    public function inmultesteCu($factor) { return Bani::dinBani((int)round($this->laBani() * $factor)); }
    public function compara(Bani $b) {
        $diff = $this->laBani() - $b->laBani();
        if ($diff < 0) return -1;
        if ($diff > 0) return 1;
        return 0;
    }
    public function afiseaza() { echo $this->lei . " lei si " . str_pad($this->bani, 2, "0", STR_PAD_LEFT) . " bani"; }
}
$pub = (int)($ctx['scope']['counts']['published_count'] ?? 0);
$draft = (int)($ctx['scope']['counts']['draft_count'] ?? 0);
$b1 = new Bani(100 + $pub, 50);
$b2 = new Bani(45 + $draft, 75);
echo "b1 = "; $b1->afiseaza(); echo "\n";
echo "b2 = "; $b2->afiseaza(); echo "\n";
$suma = $b1->aduna($b2); echo "b1 + b2 = "; $suma->afiseaza(); echo "\n";
$dif = $b1->scade($b2); echo "b1 - b2 = "; $dif->afiseaza(); echo "\n";
$jum = $b1->imparteLaNumar(2); echo "b1 / 2 = "; if ($jum) $jum->afiseaza(); echo "\n";
$dbl = $b1->inmultesteCu(1.5); echo "b1 * 1.5 = "; $dbl->afiseaza(); echo "\n";
echo "b1 compara b2: " . $b1->compara($b2) . " (1=mai mare, -1=mai mic, 0=egal)\n";
