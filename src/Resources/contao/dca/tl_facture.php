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
            'fields'      => array('dateEdition'),
            'panelLayout' => 'filter;sort,search,limit',
            'flag'        => 12, //https://docs.contao.org/dev/reference/dca/fields/#reference
        ),
        'label'             => array(
            'fields'         => array('dateEdition', 'enfant:tl_enfant.CONCAT(prenom, " ", nom)', 'noFacture', 'dateDebut', 'dateFin', 'total', 'estPaye', 'typePaiement'),
            'showColumns'    => true,
            'label_callback' => array('tl_facture', 'setLabels'),
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
            /*
        'paiement' => array(
        'icon'  => $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'images/euro.png',
        'label' => &$GLOBALS['TL_LANG'][$t]['paiement'],
        ),
         */
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
        'default'      => 'noFacture,dateEdition,dateDebut,dateFin,tarifs,nombreRepas,absences,adhesions,total,enfant;{paiement},datePaiement,estPaye,typePaiement',
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
        'dateEdition'  => TypeChamp::date(),
        'noFacture'    => TypeChamp::text(),
        'dateDebut'    => TypeChamp::date(true),
        'dateFin'      => TypeChamp::date(true),
        'tarifs'       => TypeChamp::selectTable('tl_tarif.nom', false, true, false),
        'nombreRepas'  => TypeChamp::number(),
        'absences'     => TypeChamp::number(),
        'adhesions'    => TypeChamp::number(),
        'total'        => TypeChamp::number(),
        'enfant'       => TypeChamp::selectTable('tl_enfant.CONCAT(prenom, " ", nom)', false, true),
        'datePaiement' => TypeChamp::date(),
        'estPaye'      => TypeChamp::ouiNon(),
        'typePaiement' => TypeChamp::select($GLOBALS['typePaiements'], false, true),
    ),
);

class tl_facture extends Backend
{
    public function setLabels($row, $label, DataContainer $dc, $args)
    {
        $args[0] = date('d/m/Y', $args[0]);

        $args[3] = date('d/m/Y', $args[3]);
        $args[4] = date('d/m/Y', $args[4]);

        $args[5] = $args[5] . ' €';
        $args[6] = ($args[6] == 'non') ? 'Non' : 'Oui';
        return $args;
    }

}
