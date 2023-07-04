<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['noFacture'][0]    = "N° Facture";
$GLOBALS['TL_LANG'][$t]['dateEdition'][0]  = "Date d'édition";
$GLOBALS['TL_LANG'][$t]['dateDebut'][0]    = "Début période";
$GLOBALS['TL_LANG'][$t]['dateFin'][0]      = "Fin période";
$GLOBALS['TL_LANG'][$t]['tarifs'][0]       = "Tarifs appliqués";
$GLOBALS['TL_LANG'][$t]['nombreRepas'][0]  = "Nombre de repas";
$GLOBALS['TL_LANG'][$t]['total'][0]        = "Total à payer";
$GLOBALS['TL_LANG'][$t]['enfant'][0]       = "Enfant";
$GLOBALS['TL_LANG'][$t]['paiement']        = "Paiement";
$GLOBALS['TL_LANG'][$t]['datePaiement'][0] = "Date du paiement";
$GLOBALS['TL_LANG'][$t]['estPaye'][0]      = "Est payé ?";
$GLOBALS['TL_LANG'][$t]['typePaiement'][0] = "Type de paiement";

//$GLOBALS['TL_LANG'][$t]['paiement'] = array(' paiement', 'Payer la facture ID %s');
