<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['nom'][0]   = "Nom";
$GLOBALS['TL_LANG'][$t]['tarif'][0] = "Tarif";
