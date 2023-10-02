<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['nom'][0]      = "Nom";
$GLOBALS['TL_LANG'][$t]['matin'][0]    = "Matin";
$GLOBALS['TL_LANG'][$t]['midi'][0]     = "Midi";
$GLOBALS['TL_LANG'][$t]['soir'][0]     = "Soir";
$GLOBALS['TL_LANG'][$t]['absence'][0]  = "Absence non justifiée";
$GLOBALS['TL_LANG'][$t]['adhesion'][0] = "Adhésion";
