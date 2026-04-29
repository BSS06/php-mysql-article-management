<?php

class Desteptator {

    private $oraAlarm;
    private $minutAlarm;

    public function __construct($ora = 7, $minut = 0) {
        if (!$this->esteValid($ora, $minut)) {
            echo "Eroare: ora sau minutul sunt invalide!\n";
            $this->oraAlarm   = 0;
            $this->minutAlarm = 0;
        } else {
            $this->oraAlarm   = (int)$ora;
            $this->minutAlarm = (int)$minut;
        }
    }

    public function esteValid($ora, $minut) {
        return ($ora >= 0 && $ora <= 23 && $minut >= 0 && $minut <= 59);
    }

    public function seteazaAlarm($ora, $minut) {
        if (!$this->esteValid($ora, $minut)) {
            echo "Eroare: ora sau minutul sunt invalide!\n";
            return;
        }
        $this->oraAlarm   = (int)$ora;
        $this->minutAlarm = (int)$minut;
    }

    public function minuteRamase() {
        $acum   = (int)date("H") * 60 + (int)date("i");
        $alarma = $this->oraAlarm * 60 + $this->minutAlarm;
        $diff   = $alarma - $acum;
        if ($diff < 0) $diff += 1440;
        return $diff;
    }

    public function afiseaza() {
        echo "Alarma setata la: "
             . str_pad($this->oraAlarm,   2, "0", STR_PAD_LEFT) . ":"
             . str_pad($this->minutAlarm, 2, "0", STR_PAD_LEFT) . "\n";
        echo "Ora curenta: " . date("H:i") . "\n";
        $min = $this->minuteRamase();
        echo "Timp ramas: " . intdiv($min, 60) . " ore si " . ($min % 60) . " minute\n";
    }
}

// --- Utilizare ---
$d = new Desteptator(8, 30);
$d->afiseaza();

echo "\n";

$d->seteazaAlarm(23, 45);
$d->afiseaza();

?>
