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

use trdev\ContaoCantineBundle\Classes\AjaxAPE;
use trdev\ContaoCantineBundle\Element\ceSuivi;
use trdev\ContaoCantineBundle\Module\beModuleSaisie;

$GLOBALS['assetsFolder']['ContaoCantineBundle']    = "/bundles/contaocantine/";
$GLOBALS['bundleNamespace']['ContaoCantineBundle'] = "trdev\\ContaoCantineBundle\\";

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array(AjaxAPE::class, 'pageLoad');

// #region STYLES
$GLOBALS['TL_CSS'][] = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200';

if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = $GLOBALS['assetsFolder']['ContaoCantineBundle'] . "css/be.css";
}
// #endregion

// #region JAVASCRIPTS
$GLOBALS['TL_JAVASCRIPT'][] = '//kit.fontawesome.com/b445ac5b2a.js';
$GLOBALS['TL_JAVASCRIPT'][] = ($_ENV['APP_ENV'] == "dev") ? $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'js/full/cantine.js' : $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'js/cantine.min.js';
/* #endregion JAVASCRIPTS */

// #region ELEMENTS
array_insert($GLOBALS['TL_CTE']['Cantine'], 1, array(
    'ceSuivi' => ceSuivi::class,
));
// #endregion

// #region FRONTEND MODULES
//$GLOBALS['FE_MOD']['Tabularasa']['ModuleSuivi'] = ModuleSuivi::class;
// #endregion

//Menu BE
array_insert($GLOBALS['BE_MOD']['apeloin'], 98, array(
    'saisie'         => array(
        'callback' => beModuleSaisie::class,
    ),
    'enfants'        => array(
        'tables' => array('tl_enfant'),
    ),
    'etablissements' => array(
        'tables' => array('tl_etablissement'),
    ),
    'classes'        => array(
        'tables' => array('tl_classe'),
    ),
    'repas'          => array(
        'tables' => array('tl_repas'),
    ),
    /*
'Facture'       => array(
'tables' => array('tl_facture'),
),*/

));
