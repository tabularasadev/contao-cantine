<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['date'][0] = "Date du repas";
//$GLOBALS['TL_LANG'][$t]['present'][0] = "Présent ?";
//$GLOBALS['TL_LANG'][$t]['repas'][0]   = "Nom du repas";
$GLOBALS['TL_LANG'][$t]['petitDej'][0]      = "Présence au petit déjeuner ?";
$GLOBALS['TL_LANG'][$t]['dejeuner'][0]      = "Présence au déjeuner ?";
$GLOBALS['TL_LANG'][$t]['gouter'][0]        = "Présence au goûter ?";
$GLOBALS['TL_LANG'][$t]['absence'][0]       = "Absence non justifiée ?";
$GLOBALS['TL_LANG'][$t]['adhesion'][0]      = "Adhésion";
$GLOBALS['TL_LANG'][$t]['classe'][0]        = "Classe de l'enfant";
$GLOBALS['TL_LANG'][$t]['etablissement'][0] = "Etablissement de l'enfant";
$GLOBALS['TL_LANG'][$t]['nomEnfant'][0]     = "Enfant";
