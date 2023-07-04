<?php

namespace trdev\ContaoCantineBundle\Module;

use Contao\Input;
use Contao\RequestToken;
use Safe\DateTime;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\factureModel;

class beModuleGenFactures extends \BackendModule
{

    protected $strTemplate = "beGenFactures";

    protected function compile()
    {
        $rt                 = new RequestToken();
        $this->Template->rt = $rt->get();

        if (null != Input::post('FORM_SUBMIT') && Input::post('FORM_SUBMIT') == 'genFacture') {
            $dateDebut = Input::post('dateDebut');
            $dateFin   = Input::post('dateFin');
            $this->generation($dateDebut, $dateFin);
        } else {
            $dateDebut = date('Y-m-01');
            $dateFin   = date('Y-m-t');
        }

        $this->Template->dateDebut = $dateDebut;
        $this->Template->dateFin   = $dateFin;
    }

    private function generation($deb, $fin)
    {
        $enfants  = enfantModel::findByScolarise('Oui');
        $factures = array();

        if ($enfants) {
            $debut = DateTime::createFromFormat('Y-m-d', $deb)->setTime(0, 0);
            $fin   = DateTime::createFromFormat('Y-m-d', $fin)->setTime(23, 59);
            foreach ($enfants as $enfant) {
                $factures[] = factureModel::nouvelleFacture($debut, $fin, $enfant);
            }
            $this->Template->factures = $factures;
        }
    }
}
