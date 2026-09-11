<?php
class Acces {
    private $utilizator;
    private $parola;
    private $rang;
    private $esteAutentificat;
    public function __construct($utilizator, $parola, $rang = "user") {
        $this->utilizator = $utilizator;
        $this->parola = hash('sha256', $parola);
        $this->rang = $rang;
        $this->esteAutentificat = false;
    }
    public function acces($parolaIntrodusa) {
        if (hash('sha256', $parolaIntrodusa) === $this->parola) {
            $this->esteAutentificat = true;
            echo "Acces acordat utilizatorului: " . $this->utilizator . "\n";
            return true;
        }
        echo "Acces refuzat! Parola incorecta.\n";
        return false;
    }
    public function modificare($parolaVeche, $parolaNoua) {
        if (!$this->esteAutentificat) { echo "Eroare: nu sunteti autentificat!\n"; return false; }
        if (hash('sha256', $parolaVeche) !== $this->parola) { echo "Eroare: parola veche incorecta!\n"; return false; }
        $this->parola = hash('sha256', $parolaNoua);
        echo "Parola a fost modificata cu succes.\n";
        return true;
    }
    public function introducere($numeNou, $parolaNou, $rangNou = "user") {
        if ($this->rang !== "admin") { echo "Eroare: doar administratorii pot adauga utilizatori!\n"; return null; }
        $nou = new Acces($numeNou, $parolaNou, $rangNou);
        echo "Utilizatorul '" . $numeNou . "' a fost adaugat cu rangul '" . $rangNou . "'.\n";
        return $nou;
    }
    public function afiseaza() {
        echo "Utilizator: " . $this->utilizator . "\n";
        echo "Rang: " . $this->rang . "\n";
        echo "Autentificat: " . ($this->esteAutentificat ? "DA" : "NU") . "\n";
    }
}
$admin = new Acces($ctx['user']['email'] ?? 'admin@portal.md', 'root', 'admin');
$admin->afiseaza();
echo "\n";
$admin->acces('parola_gresita');
$admin->acces('root');
echo "\n";
$admin->modificare('root', 'root_nou');
echo "\n";
$admin->introducere('editor.nou@portal.md', 'pass123', 'redactor');
