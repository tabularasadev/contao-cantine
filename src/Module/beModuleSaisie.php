<?php

namespace trdev\ContaoCantineBundle\Module;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Input;
use Contao\RequestToken;
use Contao\System;
use DateTime;
use Psr\Log\LogLevel;
use trdev\ContaoCantineBundle\Model\classeModel;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\etablissementModel;
use trdev\ContaoCantineBundle\Model\repasModel;

class beModuleSaisie extends \BackendModule
{

    protected $strTemplate = "beSaisie";

    protected function compile()
    {
        $this->import('BackendUser', 'User');

        /** @var \BackendTemplate|object $objTemplate */
        $objTemplate        = new \BackendTemplate('be_wildcard');
        $token              = new RequestToken();
        $this->Template->rt = $token->get();
        $champs             = array(
            'petitDej',
            'dejeuner',
            'gouter',
            'absence',
            'adhesion',
        );

        $date = new DateTime();
        $date->setTime(0, 0, 0);
        $date = $date->format('U');

        if (null != Input::post('FORM_SUBMIT') && Input::post('FORM_SUBMIT') == 'saisie_repas') {
            $enfants = Input::post('enfants');
            $date    = strtotime(Input::post('date'));
            $maj     = [];
            if (null != $enfants && Count($enfants) > 0) {
                foreach ($enfants as $unEnfant) {
                    $enf   = enfantModel::findByPk($unEnfant);
                    $choix = [];
                    foreach ($champs as $ch) {
                        $data = Input::post($ch);
                        if (null != $data && Count($data) > 0) {
                            $choix[$ch] = (in_array($unEnfant, $data)) ? 'Oui' : 'Non';
                        } else {
                            $choix[$ch] = "Non";
                        }
                    }

                    $repas = repasModel::findBy(array('nomEnfant=?', 'date=?'), array($unEnfant, $date));

                    if (in_array('Oui', $choix)) {
                        $existant = false;
                        if (!$repas) {
                            $repas            = new repasModel();
                            $repas->tstamp    = time();
                            $repas->date      = $date;
                            $repas->nomEnfant = $unEnfant;
                            $maj[]            = sprintf('Creation pour %s %s', $enf->nom, $enf->prenom);
                        } else {
                            $existant = true;
                        }
                        $i = 0;
                        foreach ($choix as $key => $value) {
                            if ($repas->{$key} != $value) {
                                $i++;
                                $repas->{$key} = $value;
                            }
                        }

                        if ($i > 0 && $existant == true) {
                            $maj[] = sprintf('Mise à jour pour %s %s', $enf->nom, $enf->prenom);
                        }

                        $repas->save();

                    } else {
                        if ($repas) {
                            $repas->delete();
                            $maj[] = sprintf('Suppression pour %s %s', $enf->nom, $enf->prenom);
                        }
                    }
                }
                $jsonLog = [
                    'maj' => sprintf('Mise a jour de la part de %s pour la date du %s', $this->User->username, Input::post('date')),
                    'enf' => $maj,
                ];
                $this->sendLog(json_encode($jsonLog));
            }
        }

        $this->Template->classe = classeModel::findAll();
        if ($this->User->etablissement != '' && $this->User->etablissement != '0') {
            $this->Template->enfant        = enfantModel::findBy(['scolarise=?', 'etablissement=?'], ['Oui', $this->User->etablissement], array('order' => 'nom ASC, prenom ASC'));
            $this->Template->etablissement = etablissementModel::findByPk($this->User->etablissement);
        } else {
            $this->Template->enfant         = enfantModel::findByScolarise('Oui', array('order' => 'nom ASC, prenom ASC'));
            $this->Template->etablissements = etablissementModel::findAll();
        }

        $this->Template->date = $date;

        $repas = repasModel::findByDate($date);
        if ($repas) {
            $listeRepas = array(
                'petitDej' => array(),
                'dejeuner' => array(),
                'gouter'   => array(),
                'absence'  => array(),
                'adhesion' => array(),
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

    private function sendLog($texte)
    {
        $this->container  = System::getContainer();
        $this->logger     = $this->container->get('monolog.logger.contao');
        $this->projectDir = $this->container->getParameter('kernel.project_dir');

        $this->logger->log(LogLevel::INFO, $texte, array('contao' => new ContaoContext('Cantine', "MAJ")));
    }
}
