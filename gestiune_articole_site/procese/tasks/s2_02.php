<?php
class PortalEditorial {
    public $nume;
    public $suprafata;
    public $populatie;
    public $capitala;
    public $limba_oficiala;
    public function __construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala) {
        $this->nume = $nume; $this->suprafata = $suprafata; $this->populatie = $populatie; $this->capitala = $capitala; $this->limba_oficiala = $limba_oficiala;
    }
    public function afiseazaInfo() { echo "Portal: {$this->nume}, Home: {$this->capitala}, Limba: {$this->limba_oficiala}\n"; }
    public function calculeazaDensitate() { return round($this->populatie / max(1, $this->suprafata), 2); }
    public function afiseazaSuprafata() { echo "Suprafata: {$this->suprafata} widget-uri\n"; }
}
class SectiunePortal extends PortalEditorial {
    public $centru_raional;
    public $numar_localitati;
    public $cod_postal;
    public function __construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala, $centru_raional, $numar_localitati, $cod_postal) {
        parent::__construct($nume, $suprafata, $populatie, $capitala, $limba_oficiala);
        $this->centru_raional = $centru_raional; $this->numar_localitati = $numar_localitati; $this->cod_postal = $cod_postal;
    }
    public function afiseazaRaion() { echo "Sectiune: {$this->nume}, Centru: {$this->centru_raional}, Articole: {$this->numar_localitati}\n"; }
    public function afiseazaCodPostal() { echo "Cod sectiune: {$this->cod_postal}\n"; }
}
$c1 = procese_pick_category($ctx, 0); $c2 = procese_pick_category($ctx, 1); $c3 = procese_pick_category($ctx, 2);
$rm1 = new PortalEditorial('Portal Editorial', 12, (int)($ctx['scope']['counts']['published_count'] ?? 1), 'index.php', 'Romana');
$rm2 = new PortalEditorial('Portal Analitic', 10, (int)($ctx['scope']['counts']['draft_count'] ?? 1), 'dashboard', 'Romana');
$rm3 = new PortalEditorial('Portal Interactiuni', 8, (int)($ctx['scope']['counts']['approved_comments'] ?? 1), 'comentarii', 'Romana');
$rm1->afiseazaInfo(); $rm2->afiseazaInfo(); $rm3->afiseazaInfo();
echo "Densitatea continutului in portal: " . $rm1->calculeazaDensitate() . " obiecte/widget\n";
$rm1->afiseazaSuprafata();
echo "\n";
$raion1 = new SectiunePortal($c1['nume_categorie'], 6, (int)$c1['total_articole'], 'index.php', 'Romana', 'editorial', (int)$c1['total_articole'], 'CAT-' . $c1['id_categorie']);
$raion2 = new SectiunePortal($c2['nume_categorie'], 6, (int)$c2['total_articole'], 'index.php', 'Romana', 'editorial', (int)$c2['total_articole'], 'CAT-' . $c2['id_categorie']);
$raion1->afiseazaRaion(); $raion1->afiseazaCodPostal(); $raion2->afiseazaRaion(); $raion2->afiseazaCodPostal();
