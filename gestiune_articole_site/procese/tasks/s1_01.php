<?php
$articol1 = procese_pick_article($ctx, 0);
$articol2 = procese_pick_article($ctx, 1);
class ArticolEditorial {
    public $titlu;
    public $vechime_zile;
    public $vizualizari;
}
$primul = new ArticolEditorial();
$primul->titlu = $articol1['titlu'];
$primul->vechime_zile = max(1, (int)((time() - strtotime($articol1['data_publicarii'] ?: date('Y-m-d H:i:s'))) / 86400));
$primul->vizualizari = (int)$articol1['numar_vizualizari'];
$alDoilea = new ArticolEditorial();
$alDoilea->titlu = $articol2['titlu'];
$alDoilea->vechime_zile = max(1, (int)((time() - strtotime($articol2['data_publicarii'] ?: date('Y-m-d H:i:s'))) / 86400));
$alDoilea->vizualizari = (int)$articol2['numar_vizualizari'];
echo "Vizualizari pentru '{$primul->titlu}': " . $primul->vizualizari . "\n";
echo "Vizualizari pentru '{$alDoilea->titlu}': " . $alDoilea->vizualizari . "\n";
echo "Suma vechimii articolelor: " . ($primul->vechime_zile + $alDoilea->vechime_zile) . " zile\n";
echo "\n";
class ArticolEditorialConstruct {
    public $titlu;
    public $vechime_zile;
    public $vizualizari;
    public function __construct($titlu, $vechime_zile, $vizualizari) {
        $this->titlu = $titlu;
        $this->vechime_zile = $vechime_zile;
        $this->vizualizari = $vizualizari;
    }
}
$primul2 = new ArticolEditorialConstruct($primul->titlu, $primul->vechime_zile, $primul->vizualizari);
$alDoilea2 = new ArticolEditorialConstruct($alDoilea->titlu, $alDoilea->vechime_zile, $alDoilea->vizualizari);
echo "Vizualizari pentru '{$primul2->titlu}' (construct): " . $primul2->vizualizari . "\n";
echo "Vizualizari pentru '{$alDoilea2->titlu}' (construct): " . $alDoilea2->vizualizari . "\n";
echo "Suma vechimii articolelor (construct): " . ($primul2->vechime_zile + $alDoilea2->vechime_zile) . " zile\n";
