<?php

namespace trdev\ContaoCantineBundle\Module;

use Contao\Input;
use Contao\RequestToken;
use DateTime;
use trdev\ContaoCantineBundle\Model\classeModel;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\etablissementModel;
use trdev\ContaoCantineBundle\Model\repasModel;

class beModuleSaisie extends \BackendModule
{

    protected $strTemplate = "beSaisie";

    protected function compile()
    {
        /** @var \BackendTemplate|object $objTemplate */
        $objTemplate        = new \BackendTemplate('be_wildcard');
        $token              = new RequestToken();
        $this->Template->rt = $token->get();
        $champs             = array(
            'petitDej',
            'dejeuner',
            'gouter',
        );
        if (null != Input::post('FORM_SUBMIT') && Input::post('FORM_SUBMIT') == 'saisie_repas') {
            $enfants = Input::post('enfants');
            $date    = strtotime(Input::post('date'));
            if (null != $enfants && Count($enfants) > 0) {
                foreach ($enfants as $unEnfant) {
                    $repas = repasModel::findBy(array('nomEnfant=?', 'date=?'), array($unEnfant, $date));
                    if (!$repas) {
                        $repas            = new repasModel();
                        $repas->tstamp    = time();
                        $repas->date      = $date;
                        $repas->nomEnfant = $unEnfant;
                    }
                    foreach ($champs as $ch) {
                        $data = Input::post($ch);
                        if (null != $data && Count($data) > 0) {
                            $repas->{$ch} = (in_array($unEnfant, $data)) ? 'Oui' : 'Non';
                        } else {
                            $repas->{$ch} = "Non";
                        }
                    }
                    $repas->save();
                    if ($repas->petitDej == 'Non' && $repas->dejeuner == 'Non' && $repas->gouter == 'Non') {
                        $repas->delete();
                    }
                }
            }
        }

        $this->Template->enfant        = enfantModel::findByScolarise('Oui', array('order' => 'nom ASC, prenom ASC'));
        $this->Template->classe        = classeModel::findAll();
        $this->Template->etablissement = etablissementModel::findAll();

        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $repas = repasModel::findByDate($today->format('U'));
        if ($repas) {
            $listeRepas = array(
                'petitDej' => array(),
                'dejeuner' => array(),
                'gouter'   => array(),
            );
            foreach ($repas as $rep) {
                foreach ($champs as $ch) {
                    if ($rep->{$ch} == 'Oui') {
                        $listeRepas[$ch][] = $rep->nomEnfant;
                    }
                }
            }
            $this->Template->repas = $listeRepas;
        }
    }
}
