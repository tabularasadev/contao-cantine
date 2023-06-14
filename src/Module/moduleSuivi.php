<?php

namespace trdev;

use trdev\ContaoCantineBundle\Model\repasModel;

class moduleSuivi extends \BackendModule
{

    protected $strTemplate = "ceSuivi";

    protected function compile()
    {
        /** @var \BackendTemplate|object $objTemplate */
        $objTemplate               = new \BackendTemplate('be_wildcard');
        $this->Template->dateDebut = $_POST['dateDebut'];
        $this->Template->dateFin   = $_POST['dateFin'];
        $this->Template->all       = repasModel::getAll();
    }
}
