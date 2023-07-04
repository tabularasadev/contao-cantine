<?php

namespace trdev\ContaoCantineBundle\Classes;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Input;
use Contao\System;
use DateTime;
use Exception;
use Psr\Log\LogLevel;
use stdClass;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\factureModel;
use trdev\ContaoCantineBundle\Model\repasModel;

class AjaxAPE extends \Contao\Controller
{
    public function __construct()
    {
        $this->container  = System::getContainer();
        $this->logger     = $this->container->get('monolog.logger.contao');
        $this->projectDir = $this->container->getParameter('kernel.project_dir');

    }

    public function pageLoad($buffer, $templateName)
    {
        $pageExp = explode('?', $_SERVER["REQUEST_URI"]);
        if ($pageExp[0] == '/ajax.html') {
            $item = new self();
            $item->rqtAjax();
        }

        return $buffer;
    }

    public function rqtAjax()
    {
        $inputType = ($_GET['debug']) ? 'get' : 'post';
        $res       = array('result' => 'success', 'data' => array());
        $code      = 200;
        try {
            if (null != Input::{$inputType}('action') && Input::{$inputType}('action') != "") {
                switch (Input::{$inputType}('action')) {
                case 'getListeRepas':
                    //On récupère les enfants
                    $c = array('scolarise=?');
                    $v = array('Oui');
                    if (null != Input::{$inputType}('classe')) {
                        $c[] = 'classe=?';
                        $v[] = Input::{$inputType}('classe');
                    }
                    if (null != Input::{$inputType}('etablissement')) {
                        $c[] = 'etablissement=?';
                        $v[] = Input::{$inputType}('etablissement');
                    }
                    $enfants = enfantModel::findBy($c, $v);

                    //On récupère les repas pour chaque enfant
                    $date  = DateTime::createFromFormat('Y-m-d', Input::{$inputType}('date'))->setTime(0, 0, 0, 0)->format('U');
                    $repas = array();
                    if ($enfants) {
                        foreach ($enfants as $enf) {
                            $unRepas = repasModel::findOneBy(array(
                                'nomEnfant=?', 'date=?',
                            ), array($enf->id, $date));
                            $rp         = new stdClass();
                            $rp->enfant = $enf->id;
                            if (null != $unRepas) {
                                $rp->matin = ($unRepas->petitDej === 'Oui') ? 1 : 0;
                                $rp->midi  = ($unRepas->dejeuner === 'Oui') ? 1 : 0;
                                $rp->soir  = ($unRepas->gouter === 'Oui') ? 1 : 0;
                            } else {
                                $rp->matin = 0;
                                $rp->midi  = 0;
                                $rp->soir  = 0;
                            }
                            $repas[] = $rp;
                        }
                    }
                    $res['data'] = $repas;
                    break;
                case 'majPaiement':
                    $facture = factureModel::findByPk(Input::{$inputType}('item'));
                    $facture->setPaiement(Input::{$inputType}('choix'));
                    break;
                case 'sendMailFacture':
                    $facture = factureModel::findByPk(Input::{$inputType}('item'));
                    if (!$facture->sendMail()) {
                        throw new Exception('Erreur d\'envoi de mail', 1);
                    }
                    break;
                default:
                    throw new Exception('Action ' . Input::{$inputType}('action') . ' non paramétré', 1);
                    break;
                }
            }
        } catch (\Throwable $th) {
            $res = $this->error($th);
            $this->saveLog($th, get_class($this) . '::' . __FUNCTION__);
            $code = 500;

        }
        echo \json_encode($res);
        exit($code);
    }

    private function error($erreur)
    {
        if (is_object($erreur)) {
            return array(
                'result'  => 'error',
                'details' => array(
                    'msg'   => $erreur->getMessage(),
                    'trace' => $erreur->getTrace(),
                ),
            );
        } else {
            return array(
                'result'  => 'error',
                'details' => array(
                    'msg' => $erreur,
                ),
            );
        }
    }

    public function saveLog($th, $fonction = '')
    {
        $this->logger->log(LogLevel::ERROR, $th->getMessage(), array('contao' => new ContaoContext($fonction, "AJAX")));
    }
}

class_alias(AjaxAPE::class, 'AjaxAPE');
