<?php

namespace trdev\ContaoCantineBundle\Model;

use Contao\Email;
use Contao\RequestToken;
use Contao\System;
use DateTime;

class factureModel extends \Model
{
    protected static $strTable = 'tl_facture';

    private $nbRepasMatin   = 0;
    private $nbRepasMidi    = 0;
    private $nbRepasSoir    = 0;
    private $nbAbsences     = 0;
    private $nbAdhesions    = 0;
    private $coutRepasMatin = 0;
    private $coutRepasMidi  = 0;
    private $coutRepasSoir  = 0;
    private $coutAbsences   = 0;
    private $coutAdhesions  = 0;

    private function comptage()
    {

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

            if (isset($r->absence) && $r->absence == "Oui") {
                $this->nbAbsences++;
            }

            if (isset($r->adhesion) && $r->adhesion == "Oui") {
                $this->nbAdhesions++;
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
        if ($this->nbRepasAbsence > 0) {
            $this->coutRepasSoir = $this->nbRepasAbsence * floatval($tarifs->soir);
        }

        if ($this->nbAbsences > 0) {
            $this->coutAbsences = $this->nbAbsences * floatval($tarifs->absence);
        }

        if ($this->nbAdhesions > 0) {
            $this->coutAdhesions = $this->nbAdhesions * floatval($tarifs->adhesion);
        }

        $this->total       = $this->coutRepasMatin + $this->coutRepasMidi + $this->coutRepasSoir + $this->coutAbsences + $this->coutAdhesions;
        $this->nombreRepas = $this->nbRepasMatin + $this->nbRepasMidi + $this->nbRepasSoir;
        $this->absences    = $this->nbAbsences;
        $this->adhesions   = $this->nbAdhesions;

        if ($this->estPaye != '1') {
            $this->save();
        }

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

    public function getNbAbsences()
    {
        if (!$this->nbAbsences) {
            $this->comptage();
        }
        return $this->nbAbsences;
    }

    public function getNbAdhesions()
    {
        if (!$this->nbAdhesions) {
            $this->comptage();
        }
        return $this->nbAdhesions;
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
            case 'tarifAbsence':
                $compte = $this->tarifs->absence;
                break;
            case 'totalAbsence':
                $compte = $this->coutAbsences;
                break;
            case 'tarifAdhesion':
                $compte = $this->tarifs->adhesion;
                break;
            case 'totalAdhesion':
                $compte = $this->coutAdhesion;
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

    public function sendMail($type = 'nouveau')
    {
        $enfant = enfantModel::findByPk($this->enfant);
        $emails = explode(',', $enfant->emailParents);

        if ($emails == null || Count($emails) == 0) {
            return false;
        }

        $message = new Email();

        if ($type == 'nouveau') {
            $message->subject = $GLOBALS['TL_CONFIG']['objetMailFacture'];
            $html             = $GLOBALS['TL_CONFIG']['messageMailFacture'];
        } else {
            $message->subject = $GLOBALS['TL_CONFIG']['objetMailRelance'];
            $html             = $GLOBALS['TL_CONFIG']['messageMailRelance'];
        }

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

    public function setPaiement($choix, $date)
    {
        $date               = DateTime::createFromFormat('Y-m-d', $date);
        $this->datePaiement = $date->format('U');
        $this->estPaye      = '1';
        $this->typePaiement = $choix;
        $this->save();
    }

    public function showButtons()
    {
        $rt = new RequestToken();

        $images = array(
            'more' => $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'images/more.png',
            'fact' => $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'images/facture.png',
            'euro' => $GLOBALS['assetsFolder']['ContaoCantineBundle'] . 'images/euro.png',
        );
        $bts = array(
            sprintf('<a href="/contao?do=factures&act=edit&id=%s&rt=%s" target="_blank" title="En savoir plus"><img src="%s" /></a>', $this->id, $rt->get(), $images['more']),
            sprintf('<a href="%s" target="_blank" title="Voir la facture"><img src="%s" /></a>', $this->getLienPublique(), $images['fact']),
        );

        if ($this->estPaye != '1') {
            $bts[] = sprintf('<a href="/contao?do=factures&act=edit&id=%s&rt=%s" class="paiement" target="_blank" title="Régler la facture"><img src="%s"/></a>', $this->id, $rt->get(), $images['euro']);
        }

        return implode($bts);
    }
}

class_alias(factureModel::class, 'factureModel');
