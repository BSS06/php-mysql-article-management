<?php
class Acces {
    private $utilizator;
    private $parola;
    private $rang;
    private $esteAutentificat;
    public function __construct($utilizator, $parola, $rang = "user") {
        $this->utilizator       = $utilizator;
        $this->parola           = md5($parola);
        $this->rang             = $rang;
        $this->esteAutentificat = false;
    }
    public function acces($parolaIntrodusa) {
        if (md5($parolaIntrodusa) === $this->parola) {
            $this->esteAutentificat = true;
            echo "Acces acordat utilizatorului: " . $this->utilizator . "\n";
            return true;
        }
        echo "Acces refuzat! Parola incorecta.\n";
        return false;
    }
    public function modificare($parolaVeche, $parolaNoua) {
        if (!$this->esteAutentificat) {
            echo "Eroare: nu sunteti autentificat!\n";
            return false;
        }
        if (md5($parolaVeche) !== $this->parola) {
            echo "Eroare: parola veche incorecta!\n";
            return false;
        }
        $this->parola = md5($parolaNoua);
        echo "Parola a fost modificata cu succes.\n";
        return true;
    }
    public function introducere($numeNou, $parolaNou, $rangNou = "user") {
        if ($this->rang !== "admin") {
            echo "Eroare: doar administratorii pot adauga utilizatori!\n";
            return null;
        }
        $nou = new Acces($numeNou, $parolaNou, $rangNou);
        echo "Utilizatorul '" . $numeNou . "' a fost adaugat cu rangul '" . $rangNou . "'.\n";
        return $nou;
    }
    public function afiseaza() {
        echo "Utilizator: "    . $this->utilizator . "\n";
        echo "Rang: "          . $this->rang       . "\n";
        echo "Autentificat: "  . ($this->esteAutentificat ? "DA" : "NU") . "\n";
    }
}
$admin = new Acces("admin", "parola123", "admin");
$admin->afiseaza();
echo "\n";
$admin->acces("parola_gresita");
$admin->acces("parola123");
echo "\n";
$admin->modificare("parola123", "novaParola456");
echo "\n";
$admin->introducere("user_nou", "pass123", "user");
?>