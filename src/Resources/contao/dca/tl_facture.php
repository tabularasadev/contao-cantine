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
        'cTable'           => array('tl_enfant'),
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
            'fields'      => array('date'),
            'panelLayout' => 'filter;sort,search,limit',
            'flag'        => 12, //https://docs.contao.org/dev/reference/dca/fields/#reference
        ),
        'label'             => array(
            'fields'         => array('nomEnfant:tl_enfant.CONCAT(prenom, " ", nom)', 'date', 'nbrRepas', 'montant', 'statut', 'datePaiement', 'type'),
            'showColumns'    => true,
            'label_callback' => array('tl_facture', 'dateTime'),
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
        'default'      => '{Base},nbrRepas,date,nomEnfant,montant,statut, datePaiement, type',
    ),

    // Subpalettes
    'subpalettes' => array(
        '' => 'text',
    ),

    // Fields
    'fields'      => array(
        'id'           => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'       => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'date'         => TypeChamp::date(),
        'type'         => TypeChamp::select(array('', 'Espece', 'Chèque', 'Virement')),
        'montant'      => TypeChamp::text(),
        'datePaiement' => TypeChamp::date(),
        'statut'       => TypeChamp::select(array('Impayées', 'Payé')),
        'nomEnfant'    => TypeChamp::selectTable('tl_enfant.CONCAT(prenom, " ", nom)', false, true),
        'nbrRepas'     => TypeChamp::text(),
    ),
);

class tl_facture extends Backend
{
    public function dateTime($row, $label, DataContainer $dc, $args)
    {
        $args[1] = date('d/m/Y', $args['1']);
        $args[5] = date('d/m/Y', $args['5']);

        return $args;
    }

}
