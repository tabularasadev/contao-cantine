<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['nom'][0]           = "Nom";
$GLOBALS['TL_LANG'][$t]['prenom'][0]        = "Prenom";
$GLOBALS['TL_LANG'][$t]['scolarise'][0]     = "Est ce que l'enfant est scolarisé ?";
$GLOBALS['TL_LANG'][$t]['mail1'][0]         = "Mail du parent 1";
$GLOBALS['TL_LANG'][$t]['mail2'][0]         = "Mail du parent 2";
$GLOBALS['TL_LANG'][$t]['classe'][0]        = "Classe de l'enfant";
$GLOBALS['TL_LANG'][$t]['etablissement'][0] = "Etablissement de l'enfant";
