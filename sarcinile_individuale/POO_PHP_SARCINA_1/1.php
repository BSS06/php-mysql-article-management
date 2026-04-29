<?php
class Angajat {
    public $nume;
    public $virsta;
    public $salariu;
}
$ion = new Angajat();
$ion->nume = "Ion";
$ion->virsta = 25;
$ion->salariu = 1000;
$victor = new Angajat();
$victor->nume = "Victor";
$victor->virsta = 26;
$victor->salariu = 2000;
echo "Salariul lui Ion: " . $ion->salariu . "\n";
echo "Salariul lui Victor: " . $victor->salariu . "\n";
echo "Suma varstelor lui Ion si Victor: " . ($ion->virsta + $victor->virsta) . "\n";
echo "\n";
class AngajatConstruct {
    public $nume;
    public $virsta;
    public $salariu;

    public function __construct($nume, $virsta, $salariu) {
        $this->nume = $nume;
        $this->virsta = $virsta;
        $this->salariu = $salariu;
    }
}
$ion2 = new AngajatConstruct("Ion", 25, 1000);
$victor2 = new AngajatConstruct("Victor", 26, 2000);
echo "Salariul lui Ion (construct): " . $ion2->salariu . "\n";
echo "Salariul lui Victor (construct): " . $victor2->salariu . "\n";
echo "Suma varstelor lui Ion si Victor (construct): " . ($ion2->virsta + $victor2->virsta) . "\n";
