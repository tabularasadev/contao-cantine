<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['objetMailFacture'][0]   = "Objet nouvelle facture";
$GLOBALS['TL_LANG'][$t]['messageMailFacture'][0] = "Message nouvelle facture";
