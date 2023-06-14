<?php

namespace trdev\ContaoCantineBundle\Element;

use trdev\ContaoCantineBundle\Model\classeModel;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\etablissementModel;
use trdev\ContaoCantineBundle\Model\repasModel;

class ceCantine extends \ContentElement
{
    protected $strTemplate = "ceCantine";
    protected $tableau     = [];

    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### Listing cantine ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        global $objPage;

        $this->Template                = new \FrontendTemplate($this->strTemplate);
        $this->Template->rt            = "{{request_token}}";
        $this->Template->enfant        = enfantModel::findAll();
        $this->Template->classe        = classeModel::findAll();
        $this->Template->etablissement = etablissementModel::findAll();
        $this->Template->repas         = repasModel::findAll();

        $this->Template->listeRepas = $GLOBALS['listeRepas'];
        $this->Template->presence   = $GLOBALS['presence'];

        if (isset($_POST['FORM_SUBMIT']) && $_POST['FORM_SUBMIT'] == 'listing') {
            if (isset($_POST['petitDej'])) {
                foreach ($_POST['petitDej'] as $key) {
                    $this->getRepas("petitDej", $key);
                }
            }
            if (isset($_POST['dejeuner'])) {
                foreach ($_POST['dejeuner'] as $key) {
                    $this->getRepas("dejeuner", $key);
                }
            }
            if (isset($_POST['gouter'])) {
                foreach ($_POST['gouter'] as $key) {
                    $this->getRepas("gouter", $key);
                }
            }
        }
    }

    public function getRepas($type, $key)
    {
        if (isset($tableau[$key])) {
            $repas = $tableau[$key];
        } else {
            $repas            = new repasModel();
            $repas->tstamp    = time();
            $repas->date      = strtotime($_POST['date']);
            $repas->nomEnfant = $key;
        }
        $repas->{$type} = 'Oui';
        $repas->save();
        $this->tableau[$key] = $repas;
    }
}
