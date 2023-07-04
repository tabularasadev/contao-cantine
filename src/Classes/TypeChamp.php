<?php
/**
 * Contao Open Source CMS
 *
 *
 * @package   Contao
 * @author    Jimmy Nogherot
 * @license   Not free
 * @copyright Tabula Rasa
 */

namespace trdev\ContaoCantineBundle\Classes;

class TypeChamp extends \Backend
{
    #region Texte
    public static function text($obligatoire = false)
    {
        $item = array(
            'inputType' => 'text',
            'search'    => true,
            'eval'      => array('maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => $obligatoire),
            'sql'       => "varchar(255) NOT NULL default ''",
        );

        return $item;
    }
    #endregion

    #region Number
    public static function number($obligatoire = false)
    {
        $item = array(
            'inputType' => 'text',
            'search'    => true,
            'eval'      => array('maxlength' => 255, 'rgxp' => 'digit', 'tl_class' => 'w50', 'mandatory' => $obligatoire),
            'sql'       => "float(10) unsigned NOT NULL default '0'",
        );

        return $item;
    }
    #endregion

    #region TexteArea
    public static function textarea($tinyMce = false, $obligatoire = false)
    {
        $item = array(
            'inputType'     => 'textarea',
            'eval'          => array(
                'tl_class'  => 'clr',
                'mandatory' => $obligatoire,
            ),
            'load_callback' => array(array('trdev\ContaoCantineBundle\Classes\TypeChamp', 'convertAbsoluteLinks')),
            'save_callback' => array(array('trdev\ContaoCantineBundle\Classes\TypeChamp', 'convertRelativeLinks')),
            'sql'           => "mediumtext NULL",
        );

        if ($tinyMce === true) {
            $item['eval']['rte']        = 'tinyNews';
            $item['eval']['helpwizard'] = true;
        }

        return $item;
    }
    #endregion

    #region Switch
    public function ouiNon($obligatoire = false, $default = '0')
    {
        $item = array(
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default '" . $default . "'",
            'eval'      => array('tl_class' => 'clr', 'mandatory' => $obligatoire),
        );

        return $item;
    }
    #endregion

    #region Date
    public static function date($heures = false, $obligatoire = false, $classe = 'w50')
    {
        $rgxp = ($heures === true) ? 'datim' : 'date';
        $item = array(
            'inputType' => 'text',
            'eval'      => array('rgxp' => $rgxp, 'datepicker' => true, 'tl_class' => $classe, 'mandatory' => $obligatoire),
            'sql'       => "varchar(11) NOT NULL default ''",
        );

        return $item;
    }
    #endregion

    #region Select a partir d'une table
    public static function selectTable($foreignKey = '', $multiple = false, $includeBlankOption = false, $obligatoire = false)
    {
        $item = array(
            'inputType'  => 'select',
            'filter'     => true,
            'foreignKey' => $foreignKey,
            'sql'        => ($multiple === true) ? "blob NULL" : "int(10) unsigned NOT NULL default '0'",
            'eval'       => array('chosen' => true, 'includeBlankOption' => $includeBlankOption, 'multiple' => $multiple, 'tl_class' => 'w50', 'mandatory' => $obligatoire),
            'relation'   => array('type' => 'hasOne', 'load' => 'lazy'),
        );

        return $item;
    }
    #endregion

    #region Select
    public static function select($options, $multiple = false, $includeBlankOption = false, $obligatoire = false, $autosubmit = false)
    {
        $item = array(
            'inputType' => 'select',
            'filter'    => true,
            'options'   => $options,
            'sql'       => ($multiple === true) ? "blob NULL" : "varchar(255) NOT NULL default ''",
            'eval'      => array('chosen' => true, 'includeBlankOption' => $includeBlankOption, 'multiple' => $multiple, 'tl_class' => 'w50', 'mandatory' => $obligatoire, 'submitOnChange' => $autosubmit),
        );

        return $item;
    }
    #endregion

    #region Fichiers
    public static function fichier($multiple = false, $obligatoire = false)
    {
        $item = array(
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => [
                'tl_class'   => 'clr',
                'mandatory'  => $obligatoire,
                'fieldType'  => ($multiple === true) ? 'checkbox' : 'radio',
                'multiple'   => $multiple,
                'files'      => true,
                'extensions' => \Config::get('uploadTypes'),
                //'path'      => sprintf('/files/uploads/%s/%s/', date('Y'), date('m')),
            ],
            'sql'       => "blob NULL",
        );
        /*
        $item = array(
        'exclude'   => true,
        'inputType' => 'multifileupload',
        'eval'      => [
        'tl_class'     => 'clr',
        'fieldType'    => ($multiple === true) ? 'checkbox' : 'radio',
        'uploadFolder' => sprintf('/files/uploads/%s/%s/', date('Y'), date('m')),
        'labels'       => array(
        'head' => 'Pour télécharger un fichier, déposez-le ici ou cliquez sur le champ.',
        ),
        ],
        'sql'       => "blob NULL",
        );
         */
        return $item;
    }
    #endregion

    #region Published
    public function published()
    {
        return array(
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''",
            'eval'      => array('tl_class' => 'w50'),
        );
    }
    #endregion

    #region Multicolumn
    public static function mcw($table, $champ, $cols)
    {
        $item = array(
            'exclude'   => true,
            'inputType' => 'multiColumnWizard',
            'eval'      => array
            (
                'tl_class' => 'clr',
            ),
            'sql'       => "blob NULL",
        );

        foreach ($cols as $key => $value) {
            $val = array(
                'label' => &$GLOBALS['TL_LANG'][$table][$champ][$key],
            );
            switch ($value) {
                case 'date':
                    $val['inputType'] = 'text';
                    $val['eval']      = array(
                        'rgxp'       => 'date',
                        'datepicker' => true,
                        'tl_class'   => 'wizard',
                    );
                    break;
                default:
                    $val['inputType'] = $value;
                    break;
            }
            $item['eval']['columnFields'][$key] = $val;
        }
        return $item;
    }
    #endregion

    #region Alias
    public static function alias($table)
    {
        $item = array(
            'inputType'     => 'text',
            'search'        => true,
            'sql'           => "varchar(255) NOT NULL default ''",
            'eval'          => array('rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'save_callback' => array
            (
                array($table, 'generateAlias'),
            ),
        );

        return $item;
    }
    #endregion

    #region Traductions = Ajouter les traductions de base, commune a une grande majorité des tables
    public static function traductions($t)
    {
        $GLOBALS['TL_LANG'][$t]['name'][0]      = "Nom";
        $GLOBALS['TL_LANG'][$t]['alias'][0]     = "Alias";
        $GLOBALS['TL_LANG'][$t]['published'][0] = "Publié ?";
        $GLOBALS['TL_LANG'][$t]['tstamp'][0]    = "Date de création";

        $GLOBALS['TL_LANG'][$t]['new']     = array('Nouveau ', 'Ajouter');
        $GLOBALS['TL_LANG'][$t]['show']    = array(' détails', 'Montrer les détails  ID %s');
        $GLOBALS['TL_LANG'][$t]['edit']    = array('Editer ', 'Editer  ID %s');
        $GLOBALS['TL_LANG'][$t]['cut']     = array('Déplacer ', 'Déplacer  ID %s');
        $GLOBALS['TL_LANG'][$t]['copy']    = array('Dupliquer ', 'Dupliquer  ID %s');
        $GLOBALS['TL_LANG'][$t]['delete']  = array('Effacer ', 'Effacer  ID %s');
        $GLOBALS['TL_LANG'][$t]['toggle']  = array('Publier ', 'Publier  ID %s');
        $GLOBALS['TL_LANG'][$t]['feature'] = array('Mise en avant ', 'Mettre en avant ID %s');
    }
    #endregion

    public static function printIcon($fichier)
    {
        return sprintf('%simg/%s', $GLOBALS['assetsFolder']['ContaoCantineBundle'], $fichier);
    }

    public function convertAbsoluteLinks($strContent)
    {
        return str_replace('src="' . \Environment::get('base'), 'src="', $strContent);
    }

    public function convertRelativeLinks($strContent)
    {
        return $this->convertRelativeUrls($strContent);
    }
}

class_alias(TypeChamp::class, 'TypeChamp');
