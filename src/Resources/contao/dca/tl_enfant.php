<?php

/**
 * Contao Open Source CMS
 *
 * @author    Jimmy Nogherot
 * @license   Payant
 * @copyright Tabula Rasa
 */

use trdev\ContaoCantineBundle\Classes\TypeChamp;

$t = basename(__FILE__, '.php');

/**
 * Table tl_bien
 */
$GLOBALS['TL_DCA'][$t] = array(

    // Config
    'config'      => array(
        'dataContainer'    => 'Table',
        'enableVersioning' => false, //True si tu veux du versionning
        'cTable'           => array('tl_classe', 'tl_etablissement'),
        'pTable'           => array('tl_repas', 'tl_facture'),
        'sql'              => array(
            'keys' => array(
                'id' => 'primary',
            ),
        ),
    ),

    // List
    'list'        => array(
        'sorting'           => array(
            'mode'        => 1,
            'fields'      => array('nom'),
            'panelLayout' => 'filter;sort,search,limit',
            'flag'        => 12, //https://docs.contao.org/dev/reference/dca/fields/#reference
        ),
        'label'             => array(
            'fields'      => array('nom', 'prenom', 'scolarise', 'etablissement:tl_etablissement.nom', 'classe:tl_classe.nom'),
            'showColumns' => true,
        ),
        'global_operations' => array(
            'all' => array(
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ),
        ),
        'operations'        => array(
            'edit'   => array(
                'label' => &$GLOBALS['TL_LANG'][$t]['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'copy'   => array(
                'label' => &$GLOBALS['TL_LANG'][$t]['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete' => array(
                'label'      => &$GLOBALS['TL_LANG'][$t]['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show'   => array(
                'label' => &$GLOBALS['TL_LANG'][$t]['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),

    // Select
    'select'      => array(
        'buttons_callback' => array(),
    ),

    // Edit
    'edit'        => array(
        'buttons_callback' => array(),
    ),

    // Palettes
    'palettes'    => array(
        '__selector__' => array(''),
        'default'      => '{Base},nom,prenom, mail1, mail2;
                            {Selection}, scolarise, etablissement, classe',
    ),

    // Subpalettes
    'subpalettes' => array(
        '' => 'text',
    ),

    // Fields
    'fields'      => array(
        'id'            => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'        => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'nom'           => TypeChamp::text(),
        'prenom'        => TypeChamp::text(),
        'scolarise'     => TypeChamp::select(array('Oui', 'Non')),
        'mail1'         => TypeChamp::text(),
        'mail2'         => TypeChamp::text(),
        'etablissement' => TypeChamp::selectTable('tl_etablissement.nom', false, true),
        'classe'        => TypeChamp::selectTable('tl_classe.nom', false, true),
    ),
);

class tl_enfant extends Backend
{

}
