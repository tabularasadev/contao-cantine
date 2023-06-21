<?php

namespace trdev\ContaoCantineBundle\Module;

use Contao\Input;
use Contao\RequestToken;
use trdev\ContaoCantineBundle\Model\classeModel;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\etablissementModel;

class beModuleTableauBord extends \BackendModule
{

    protected $strTemplate = "beTableauBord";

    protected function compile()
    {
        $rt                        = new RequestToken();
        $this->Template->rt        = $rt->get();
        $this->Template->enfants   = enfantModel::findByScolarise('Oui', array('order' => 'nom ASC, prenom ASC'));
        $this->Template->dateDebut = (null != Input::post('dateDebut')) ? Input::post('dateDebut') : $this->getDateDebut();
        $this->Template->dateFin   = (null != Input::post('dateFin')) ? Input::post('dateFin') : $this->getDateFin();
        $etabs                     = array();
        foreach (etablissementModel::findAll() as $e) {
            $etabs[$e->id] = $e->nom;
        }
        $this->Template->etablissements = $etabs;
        $classes                        = array();
        foreach (classeModel::findAll() as $c) {
            $classes[$c->id] = $c->nom;
        }
        $this->Template->classes = $classes;
    }

    private function getDateDebut()
    {
        return date('Y-m-d', strtotime(date('Y-m-1')));
    }

    private function getDateFin()
    {
        return date('Y-m-d', strtotime(date('Y-m-30')));
    }
}
