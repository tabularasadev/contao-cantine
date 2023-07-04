<?php

$t = basename(__FILE__, '.php');

trdev\ContaoCantineBundle\Classes\TypeChamp::traductions($t);

$GLOBALS['TL_LANG'][$t]['nom'][0]                = "Nom";
$GLOBALS['TL_LANG'][$t]['prenom'][0]             = "Prenom";
$GLOBALS['TL_LANG'][$t]['scolarise'][0]          = "Est ce que l'enfant est scolarisé ?";
$GLOBALS['TL_LANG'][$t]['scolarise'][1]          = "Détermine si l'enfant est visible ou non lors de la saisie des repas";
$GLOBALS['TL_LANG'][$t]['classe'][0]             = "Classe de l'enfant";
$GLOBALS['TL_LANG'][$t]['etablissement'][0]      = "Etablissement de l'enfant";
$GLOBALS['TL_LANG'][$t]['parents']               = "Parents";
$GLOBALS['TL_LANG'][$t]['nomParent1'][0]         = "Nom du parent 1";
$GLOBALS['TL_LANG'][$t]['prenomParent1'][0]      = "Prénom du parent 1";
$GLOBALS['TL_LANG'][$t]['nomParent2'][0]         = "Nom du parent 2";
$GLOBALS['TL_LANG'][$t]['prenomParent2'][0]      = "Prénom du parent 2";
$GLOBALS['TL_LANG'][$t]['emailParents'][0]       = "Emails des parents";
$GLOBALS['TL_LANG'][$t]['emailParents'][1]       = "Séparer par une virgule";
$GLOBALS['TL_LANG'][$t]['adresseFacturation'][0] = "Adresse de facturation";
$GLOBALS['TL_LANG'][$t]['adresseFacturation'][1] = "Adresse postale, présente sur la facture";

$GLOBALS['TL_LANG'][$t]['system']     = "Champs Système";
$GLOBALS['TL_LANG'][$t]['mail1'][0]   = "Mail du parent 1";
$GLOBALS['TL_LANG'][$t]['mail2'][0]   = "Mail du parent 2";
$GLOBALS['TL_LANG'][$t]['parent1'][0] = "Compte du parent 1";
$GLOBALS['TL_LANG'][$t]['parent2'][0] = "Compte du parent 2";
