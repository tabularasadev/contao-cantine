<?php

/**
 * Contao Open Source CMS
 *
 * @author    Jimmy Nogherot
 * @license   Payant
 * @copyright Tabula Rasa
 */

use Contao\RequestToken;
use trdev\ContaoCantineBundle\Classes\TypeChamp;
use trdev\ContaoCantineBundle\Model\enfantModel;

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
            'fields'      => array('nom', 'prenom'),
            'panelLayout' => 'filter;sort,search,limit',
            'flag'        => 11, //https://docs.contao.org/dev/reference/dca/fields/#reference
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
        'buttons_callback' => array
        (
            array("$t", 'modButtons'),
        ),
    ),

    // Palettes
    'palettes'    => array(
        '__selector__' => array(),
        'default'      => 'nom,prenom,scolarise,etablissement,classe;{parents},nomParent1,prenomParent1,nomParent2,prenomParent2,emailParents,adresseFacturation;{system:hide},alias',
    ),

    // Subpalettes
    'subpalettes' => array(
    ),

    // Fields
    'fields'      => array(
        'id'                 => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'             => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'nom'                => TypeChamp::text(true),
        'prenom'             => TypeChamp::text(true),
        'scolarise'          => TypeChamp::select(array('Oui', 'Non')),
        'etablissement'      => TypeChamp::selectTable('tl_etablissement.nom', false, true, true),
        'classe'             => TypeChamp::selectTable('tl_classe.nom', false, true, true),
        'nomParent1'         => TypeChamp::text(true),
        'prenomParent1'      => TypeChamp::text(true),
        'nomParent2'         => TypeChamp::text(),
        'prenomParent2'      => TypeChamp::text(),
        'emailParents'       => TypeChamp::text(),
        'adresseFacturation' => TypeChamp::textarea(true),
        'parent1'            => TypeChamp::selectTable('tl_member.username', false, true),
        'parent2'            => TypeChamp::selectTable('tl_member.username', false, true),
        'alias'              => TypeChamp::alias($t),
    ),
);

class tl_enfant extends Backend
{
    public function generateAlias($varValue, DataContainer $dc)
    {
        if ($varValue == '') {
            $varValue = enfantModel::generateAlias();
        }
        return $varValue;
    }

    public function modButtons($arrButtons, DataContainer $dc)
    {
        $rt = new RequestToken();
        //Boutons à dégager pour faire de la place.
        //$retraits = array('saveNclose', 'saveNcreate');
        //foreach ($retraits as $r) {
        //    unset($arrButtons[$r]);
        //}

        $bts = array();

        if ($dc->activeRecord->parent1 != '0') {
            $bts[] = array(
                'name'  => 'accederAuCompte',
                'label' => 'Compte Parent 1',
                'href'  => sprintf('/contao?do=member&act=edit&id=%s&rt=%s', $dc->activeRecord->parent1, $rt->get()),
            );
        }

        if ($dc->activeRecord->parent2 != '0') {
            $bts[] = array(
                'name'  => 'accederAuCompte',
                'label' => 'Compte Parent 2',
                'href'  => sprintf('/contao?do=member&act=edit&id=%s&rt=%s', $dc->activeRecord->parent2, $rt->get()),
            );
        }

        if ($dc->activeRecord->nom != '') {
            foreach ($bts as $b) {
                $id              = $b['name'];
                $txt             = $b['label'];
                $href            = ($b['href']) ? $b['href'] : '#';
                $arrButtons[$id] = sprintf('<a href="%s" id="%s" class="tl_submit" data-id="%s" data-rt="%s">%s</a>', $href, $id, $dc->activeRecord->id, $rt->get(), $txt);
            }
        }

        return $arrButtons;
    }
}
