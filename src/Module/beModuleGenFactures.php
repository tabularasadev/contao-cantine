<?php

namespace trdev\ContaoCantineBundle\Module;

use Contao\Folder;
use Contao\Input;
use Contao\RequestToken;
use Safe\DateTime;
use Shuchkin\SimpleXLSXGen;
use trdev\ContaoCantineBundle\Model\enfantModel;
use trdev\ContaoCantineBundle\Model\factureModel;
use trdev\ContaoFonctionsBundle\Util\FcAddons;

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
            $this->generation($dateDebut, $dateFin, Input::post('choix'));
        } elseif (Input::get('exportFrom') != '' && Input::get('exportTo') != '') {
            $dateDebut = Input::get('exportFrom');
            $dateFin   = Input::get('exportTo');
            $this->generation($dateDebut, $dateFin, Input::get('choix'));
            $this->export($dateDebut, $dateFin);
        } else {
            $dateDebut = date('Y-m-01');
            $dateFin   = date('Y-m-t');
        }

        $this->Template->dateDebut = $dateDebut;
        $this->Template->dateFin   = $dateFin;
    }

    private function export($dateDebut, $dateFin)
    {
        $d = DateTime::createFromFormat('Y-m-d', $dateDebut);
        $f = DateTime::createFromFormat('Y-m-d', $dateFin);

        $titre = sprintf('Factures du %s au %s', $d->format('d-m-Y'), $f->format('d-m-Y'));
        $datas = [['<center>' . $titre . '</center>'], [
            "NUMERO DE FACTURE",
            "MONTANT",
            "STATUT",
            "TYPE DE PAIEMENT",
            "DATE DU PAIEMENT",
            "NOM PRENOM",
            "ETABLISSEMENT",
            "CLASSE",
        ]];

        foreach ($this->Template->factures as $f) {
            if ($f->noFacture != '') {
                $datas[] = [
                    $f->noFacture,
                    $f->total . ' €',
                    ($f->estPaye) ? 'Payée' : 'Impayée',
                    ($f->typePaiement != '') ? $GLOBALS['typePaiements'][$f->typePaiement] : '',
                    ($f->datePaiement != '') ? date('d/m/Y', $f->datePaiement) : '',
                    $f->printNomEnfant(),
                    $f->printEtablissement(),
                    $f->printClasse(),
                ];
            }
        }

        if (Count($datas) > 2) {
            $dossier = 'files/tabularasa/documents/' . date('y');

            if (!file_exists($dossier)) {
                mkdir($dossier, 0777, true);
            }

            $folderModel = new Folder($dossier);
            if ($folderModel->isEmpty() && !$folderModel->isUnprotected()) {
                $folderModel->unprotect();
            }

            $fileName = $dossier . '/' . FcAddons::generateAlias($titre) . '.xlsx';

            SimpleXLSXGen::fromArray($datas)
                ->setTitle($titre)
                ->mergeCells('A1:H1')
                ->setSubject($titre)
                ->saveAs($fileName);

            $http = ($_SERVER['REQUEST_SCHEME'] == '') ? 'https' : 'http';
            $url  = sprintf('%s://%s/%s', $http, $_SERVER['HTTP_HOST'], $fileName);
            header('Location: ' . $url);
            exit();
        }
    }

    private function generation($deb, $fin, $choix)
    {
        $debut = DateTime::createFromFormat('Y-m-d', $deb)->setTime(0, 0);
        $fin   = DateTime::createFromFormat('Y-m-d', $fin)->setTime(23, 59);

        if ($choix == 'suppr') {
            $factures = factureModel::findBy([
                'dateDebut = ?',
                'dateFin = ?'],
                [
                    $debut->format('U'),
                    $fin->format('U'),
                ]);
            if ($factures) {
                foreach ($factures as $fac) {
                    $fac->delete();
                }
            }

            return null;
        }

        $enfants  = enfantModel::findByScolarise('Oui');
        $factures = array();
        if ($enfants) {
            foreach ($enfants as $enfant) {
                $factures[] = factureModel::nouvelleFacture($debut, $fin, $enfant, ($choix == 'liste') ? false : true);
            }
            $this->Template->factures = $factures;
        }
    }
}
