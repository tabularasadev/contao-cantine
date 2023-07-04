<?php

namespace trdev\ContaoCantineBundle\Model;

use Contao\Email;
use Contao\System;

class factureModel extends \Model
{
    protected static $strTable = 'tl_facture';

    private $nbRepasMatin   = 0;
    private $nbRepasMidi    = 0;
    private $nbRepasSoir    = 0;
    private $coutRepasMatin = 0;
    private $coutRepasMidi  = 0;
    private $coutRepasSoir  = 0;

    private function comptage()
    {
        if ($this->estPaye != '1') {
            return false;
        }

        $cols = array(
            'nomEnfant = ?',
            'date >= ?',
            'date <= ?');
        $vals = array(
            $this->enfant,
            $this->dateDebut,
            $this->dateFin);

        $repas = repasModel::findBy($cols, $vals);

        if ($repas == null) {
            return false;
        }

        foreach ($repas as $r) {
            if (isset($r->petitDej) && $r->petitDej == 'Oui') {
                $this->nbRepasMatin++;
            }
            if (isset($r->dejeuner) && $r->dejeuner == "Oui") {
                $this->nbRepasMidi++;
            }
            if (isset($r->gouter) && $r->gouter == "Oui") {
                $this->nbRepasSoir++;
            }
        }

        $tarifs = tarifModel::findByPk($this->tarifs);

        if ($tarifs == null) {
            return false;
        }

        if ($this->nbRepasMatin > 0) {
            $this->coutRepasMatin = $this->nbRepasMatin * floatval($tarifs->matin);
        }
        if ($this->nbRepasMidi > 0) {
            $this->coutRepasMidi = $this->nbRepasMidi * floatval($tarifs->midi);
        }
        if ($this->nbRepasSoir > 0) {
            $this->coutRepasSoir = $this->nbRepasSoir * floatval($tarifs->soir);
        }

        $this->total       = $this->coutRepasMatin + $this->coutRepasMidi + $this->coutRepasSoir;
        $this->nombreRepas = $this->nbRepasMatin + $this->nbRepasMidi + $this->nbRepasSoir;
        $this->save();

        return true;
    }

    public function getLienPublique()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domaine  = $_SERVER['HTTP_HOST'] . '/';
        $page     = 'factures/';
        $cle      = hash_hmac('sha256', $this->noFacture, System::getContainer()->getParameter('kernel.secret'));
        //$cle      = md5($this->noFacture);

        return sprintf('%s%s%s%s.html', $protocol, $domaine, $page, $cle);
    }

    private function genAlias()
    {
        if ($this->noFacture == '') {
            $format = array(
                date('ym'),
                enfantModel::getInitialeByPk($this->enfant),
            );
            $alias    = implode('', $format);
            $factures = self::findBy(array('noFacture LIKE ?'), array($alias . '%'));
            if ($factures == null) {
                $alias .= '01';
            } else {
                $compte = Count($factures) + 1;
                $alias .= str_pad($compte, 2, '00', STR_PAD_LEFT);
            }
            $this->noFacture = $alias;
            $this->save();
        }
    }

    public function getByLink($item)
    {
        $factures = self::findAll(array('order' => 'id DESC'));

        if ($factures && Count($factures) > 0) {
            foreach ($factures as $fac) {
                $cle = hash_hmac('sha256', $fac->noFacture, System::getContainer()->getParameter('kernel.secret'));
                if ($fac->noFacture && $cle == $item) {
                    $fac->comptage();
                    $fac->enfant = enfantModel::findByPk($fac->enfant);
                    $fac->tarifs = tarifModel::findByPk($fac->tarifs);
                    return $fac;
                }
            }
        }
        return null;
    }

    public function getNbRepasMatin()
    {
        if (!$this->nbRepasMatin) {
            $this->comptage();
        }
        return $this->nbRepasMatin;
    }

    public function getNbRepasMidi()
    {
        if (!$this->nbRepasMidi) {
            $this->comptage();
        }
        return $this->nbRepasMidi;
    }

    public function getNbRepasSoir()
    {
        if (!$this->nbRepasSoir) {
            $this->comptage();
        }
        return $this->nbRepasSoir;
    }

    public static function nouvelleFacture($debut, $fin, $enfant)
    {
        $classe = classeModel::findByPk($enfant->classe);

        if ($classe == null) {
            return 'Enfant non assigné à une classe';
        }

        $tarif = tarifModel::findByPk($classe->tarif);

        if ($tarif == null) {
            return 'Tarif non assigné à la classe ' . $classe->nom;
        }

        $facture = self::findOneBy(array(
            'dateDebut = ?',
            'dateFin = ?',
            'enfant = ?'),
            array(
                $debut->format('U'),
                $fin->format('U'),
                $enfant->id)
        );

        if ($facture == null) {
            $facture              = new self();
            $facture->tstamp      = time();
            $facture->dateEdition = time();
            $facture->dateDebut   = $debut->format('U');
            $facture->dateFin     = $fin->format('U');
            $facture->tarifs      = $tarif->id;
            $facture->enfant      = $enfant->id;
        }

        if ($facture->comptage()) {
            $facture->genAlias();
            $facture->save();
        }

        return $facture;
    }

    public function printClasse()
    {
        if (!is_object($this->enfant)) {
            $this->enfant = enfantModel::findByPk($this->enfant);
        }
        $classe = classeModel::findByPk($this->enfant->classe);
        return $classe->nom;
    }

    public function printEmail()
    {
        if (!is_object($this->enfant)) {
            $this->enfant = enfantModel::findByPk($this->enfant);
        }
        return $this->enfant->emailParents;
    }

    public function printEtablissement()
    {
        if (!is_object($this->enfant)) {
            $this->enfant = enfantModel::findByPk($this->enfant);
        }
        $etab = etablissementModel::findByPk($this->enfant->etablissement);
        return $etab->nom;
    }

    public function printEuro($choix)
    {
        switch ($choix) {
            case 'tarifMatin':
                $compte = $this->tarifs->matin;
                break;
            case 'tarifMidi':
                $compte = $this->tarifs->midi;
                break;
            case 'tarifSoir':
                $compte = $this->tarifs->soir;
                break;
            case 'totalMatin':
                $compte = $this->coutRepasMatin;
                break;
            case 'totalMidi':
                $compte = $this->coutRepasMidi;
                break;
            case 'totalSoir':
                $compte = $this->coutRepasSoir;
                break;
            case 'total':
                $compte = $this->total;
                break;
            default:
                $compte = 0;
                break;
        }

        return str_replace('.', ',', $compte) . " €";
    }

    public function printNomEnfant()
    {
        if (!is_object($this->enfant)) {
            $this->enfant = enfantModel::findByPk($this->enfant);
        }
        return sprintf('%s %s', strtoupper($this->enfant->nom), ucfirst($this->enfant->prenom));
    }

    public function printTypePaiement()
    {
        return $GLOBALS['typePaiements'][$this->typePaiement];
    }

    public function sendMail()
    {
        $enfant = enfantModel::findByPk($this->enfant);
        $emails = explode(',', $enfant->emailParents);

        if ($emails == null || Count($emails) == 0) {
            return false;
        }

        $message          = new Email();
        $message->subject = $GLOBALS['TL_CONFIG']['objetMailFacture'];

        $html = $GLOBALS['TL_CONFIG']['messageMailFacture'];

        $datas = array(
            'lien' => sprintf("<a href='%s' target='_blank' title='Cliquez-ici pour accéder à votre facture'>Accéder à ma facture</a>", $this->getLienPublique()),
        );

        foreach ($datas as $key => $value) {
            $selecteur = sprintf('[%s]', strtoupper($key));
            $html      = str_replace($selecteur, $value, $html);
        }

        $message->html = $html;
        $res           = true;
        foreach ($emails as $e) {
            if ($_ENV['APP_ENV'] != 'dev') {
                if (!$message->sendTo($e)) {
                    $res = false;
                }
            }
        }
        return $res;
    }

    public function setPaiement($choix)
    {
        $this->datePaiement = time();
        $this->estPaye      = '1';
        $this->typePaiement = $choix;
        $this->save();
    }
}

class_alias(factureModel::class, 'factureModel');
