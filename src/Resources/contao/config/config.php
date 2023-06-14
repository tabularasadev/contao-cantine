<?php

/**
 * Contao Open Source CMS
 *
 *
 * @package   Fonctions Contao
 * @author    Jimmy Nogherot
 * @license   Not free
 * @copyright Tabula Rasa
 */

/**
 * Insert Tags
 */
//use trdev\ContaoCantineBundle

use trdev\ContaoCantineBundle\Element\ceCantine;
use trdev\ContaoCantineBundle\Element\ceSuivi;

$GLOBALS['assetsFolder']['ContaoCantineBundle']    = "/bundles/contaocantine/";
$GLOBALS['bundleNamespace']['ContaoCantineBundle'] = "trdev\\ContaoCantineBundle\\";

// #region STYLES
$GLOBALS['TL_CSS'][] = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200';
// #endregion

// #region JAVASCRIPTS
$GLOBALS['TL_JAVASCRIPT'][] = '//kit.fontawesome.com/b445ac5b2a.js';
$GLOBALS['TL_JAVASCRIPT'][] = ($_ENV['APP_ENV'] == "dev") ? $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'js/full/cantine.js' : $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'js/cantine.min.js';
/* #endregion JAVASCRIPTS */

// #region ELEMENTS
array_insert($GLOBALS['TL_CTE']['Cantine'], 1, array(
    'ceCantine' => ceCantine::class,
    'ceSuivi'   => ceSuivi::class,
));
// #endregion

// #region FRONTEND MODULES
//$GLOBALS['FE_MOD']['Tabularasa']['ModuleSuivi'] = ModuleSuivi::class;
// #endregion

// MODULE BE
array_insert($GLOBALS['BE_MOD']['moduleSuivi'], 97, array(
    'suivi' => array(
        'callback' => 'trdev\moduleSuivi',
    ),
));

//Menu BE
array_insert($GLOBALS['BE_MOD']['Renseignement'], 98, array(
    'Enfant'        => array(
        'tables' => array('tl_enfant'),
    ),
    'Etablissement' => array(
        'tables' => array('tl_etablissement'),
    ),
    'Classe'        => array(
        'tables' => array('tl_classe'),
    ),
    'Repas'         => array(
        'tables' => array('tl_repas'),
    ),
    'Facture'       => array(
        'tables' => array('tl_facture'),
    ),

));
