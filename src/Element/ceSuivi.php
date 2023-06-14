<?php

namespace trdev\ContaoCantineBundle\Element;

use trdev\ContaoCantineBundle\Model\classeModel;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\etablissementModel;
use trdev\ContaoCantineBundle\Model\repasModel;

class ceSuivi extends \ContentElement
{
    protected $strTemplate  = "ceSuivi";
    protected $pdfTemplate  = "facture";
    protected $payeTemplate = "cePaiement";

    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### Tableau de bord ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        return parent::generate();
    }

    protected function compile()
    {
        global $objPage;

        $this->Template                = new \FrontendTemplate($this->strTemplate);
        $this->Template->rt            = "{{request_token}}";
        $this->Template->enfants       = enfantModel::findAll();
        $this->Template->dateDebut     = $_POST['dateDebut'];
        $this->Template->dateFin       = $_POST['dateFin'];
        $this->Template->classe        = classeModel::findAll();
        $this->Template->etablissement = etablissementModel::findAll();
        $this->Template->repas         = repasModel::findAll();

        //$this->Template->item = new pdfTemplate(\Input::get('items'));
    }
}
