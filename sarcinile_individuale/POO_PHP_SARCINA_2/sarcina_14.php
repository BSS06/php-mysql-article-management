<?php
class PC_modern {
    protected $Nume_companie_prod;
    protected $Procesor;
    protected $Memorie;
    public function setNume_companie_prod($val) { $this->Nume_companie_prod = $val; }
    public function getNume_companie_prod() { return $this->Nume_companie_prod; }
    public function setProcesor($val) { $this->Procesor = $val; }
    public function getProcesor() { return $this->Procesor; }
    public function setMemorie($val) { $this->Memorie = $val; }
    public function getMemorie() { return $this->Memorie; }
}
class Producator_PC_modern extends PC_modern {
    private $Cost;
    public function setCost($Cost) { $this->Cost = $Cost; }
    public function getCost() { return $this->Cost; }
}
class Prod_soft extends PC_modern {
    private $Nume_Soft_pc;
    private $Pret_Soft_pc;
    public function setNume_Soft_pc($val) { $this->Nume_Soft_pc = $val; }
    public function getNume_Soft_pc() { return $this->Nume_Soft_pc; }
    public function setPret_Soft_pc($val) { $this->Pret_Soft_pc = $val; }
    public function getPret_Soft_pc() { return $this->Pret_Soft_pc; }
}
$pc1 = new Producator_PC_modern();
$pc1->setNume_companie_prod("Dell");
$pc1->setProcesor("Intel i7");
$pc1->setMemorie("16GB");
$pc1->setCost(2000);
$pc2 = new Producator_PC_modern();
$pc2->setNume_companie_prod("Samsung");
$pc2->setProcesor("Intel i5");
$pc2->setMemorie("8GB");
$pc2->setCost(1000);
echo "PC1: " . $pc1->getNume_companie_prod() . ", Procesor: " . $pc1->getProcesor() . ", Memorie: " . $pc1->getMemorie() . ", Cost: " . $pc1->getCost() . " Euro\n";
echo "PC2: " . $pc2->getNume_companie_prod() . ", Procesor: " . $pc2->getProcesor() . ", Memorie: " . $pc2->getMemorie() . ", Cost: " . $pc2->getCost() . " Euro\n";
echo "Suma costurilor PC1 si PC2: " . ($pc1->getCost() + $pc2->getCost()) . " Euro\n";
echo "\n";
$soft = new Prod_soft();
$soft->setNume_companie_prod("Microsoft");
$soft->setProcesor("N/A");
$soft->setMemorie("N/A");
$soft->setNume_Soft_pc("Windows 11");
$soft->setPret_Soft_pc(300);
echo "Soft: " . $soft->getNume_Soft_pc() . ", Pret: " . $soft->getPret_Soft_pc() . " Euro, Producator: " . $soft->getNume_companie_prod() . "\n";