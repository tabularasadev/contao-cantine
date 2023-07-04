<?php

namespace trdev\ContaoCantineBundle\Element;

use Contao\Input;
use trdev\ContaoCantineBundle\Model\factureModel;

class ceFacture extends \ContentElement
{
    protected $strTemplate = "ceFacture";

    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### FACTURES ###';
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
        if (Input::get('items')) {
            $facture = factureModel::getByLink(Input::get('items'));
            if ($facture) {
                $this->Template->facture = $facture;
            }
        }
    }
}
