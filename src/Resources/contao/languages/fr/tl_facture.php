<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['date'][0]         = "Date de la facture";
$GLOBALS['TL_LANG'][$t]['nomEnfant'][0]    = "Enfant";
$GLOBALS['TL_LANG'][$t]['montant'][0]      = "Montant de la facture";
$GLOBALS['TL_LANG'][$t]['statut'][0]       = "Statut de la facture";
$GLOBALS['TL_LANG'][$t]['type'][0]         = "Type de paiement";
$GLOBALS['TL_LANG'][$t]['datePaiement'][0] = "Date du paiement";
$GLOBALS['TL_LANG'][$t]['nbrRepas'][0]     = "Nombre de repas";
