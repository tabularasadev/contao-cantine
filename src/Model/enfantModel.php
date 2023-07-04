<?php

namespace trdev\ContaoCantineBundle\Model;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use trdev\ContaoCantineBundle\Model\repasModel;

class enfantModel extends \Model
{
    const charPwd   = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    const pwdLength = 4;

    protected static $strTable = 'tl_enfant';

    public static function getInitialeByPk($id)
    {
        $enfant = self::findByPk($id);
        if ($enfant == null) {
            return '';
        }

        return strtoupper($enfant->nom[0] . $enfant->prenom[0]);
    }

    public function getLien()
    {
        $http = ($_SERVER['REQUEST_SCHEME'] == '') ? 'https' : 'http';
        $url  = $http . '://' . $_SERVER['HTTP_HOST'];
        return $url;
    }

    public function getQrCode()
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($this->getLien())
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(250)
            ->margin(5)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false)
            ->build();
        return sprintf('<img class="qrcode" src="%s" />', $result->getDataUri());
    }

    public function countRepas($dateDebut, $dateFin, $repas = '')
    {
        $cols = array(
            'nomEnfant = ?',
            'date >= ?',
            'date <= ?');
        $vals = array(
            $this->id,
            strtotime($dateDebut),
            strtotime($dateFin));

        $nbrRepas = repasModel::findBy($cols, $vals);
        //dump($cols, $vals);
        $i = 0;
        if (isset($nbrRepas)) {
            foreach ($nbrRepas as $key) {
                if (isset($key->petitDej) && $key->petitDej == 'Oui' && ($repas == '' || $repas == 'petitDej')) {
                    $i = $i + 1;
                }
                if (isset($key->dejeuner) && $key->dejeuner == "Oui" && ($repas == '' || $repas == 'dejeuner')) {
                    $i = $i + 1;
                }
                if (isset($key->gouter) && $key->gouter == "Oui" && ($repas == '' || $repas == 'gouter')) {
                    $i = $i + 1;
                }
            }
        }
        return $i;
    }

    public static function generateAlias()
    {
        $alias = '';
        for ($i = 0; $i < self::pwdLength; $i++) {
            $alias .= substr(self::charPwd, rand(0, strlen(self::charPwd) - 1), 1);
        }

        $test = self::findByAlias($alias);
        if ($test != null) {
            $alias = self::generateID();
        }

        return $alias;
    }

    public static function getForCoupons()
    {
        $res     = array('ecoles' => array());
        $enfants = self::findByScolarise('Oui');
        if ($enfants) {
            foreach ($enfants as $e) {
                $etablissement = $e->etablissement;
                if (!$res['ecoles'][$etablissement]) {
                    $etab                          = etablissementModel::findByPk($etablissement);
                    $res['ecoles'][$etablissement] = array(
                        'nom'     => $etab->nom,
                        'classes' => array(),
                    );
                }

                $classe = $e->classe;
                if (!$res['ecoles'][$etablissement]['classes'][$classe]) {
                    $c                                                 = classeModel::findByPk($classe);
                    $res['ecoles'][$etablissement]['classes'][$classe] = array(
                        'nom'     => $c->nom,
                        'enfants' => array(),
                    );
                }

                $res['ecoles'][$etablissement]['classes'][$classe]['enfants'][] = array(
                    'nom'    => $e->nom,
                    'prenom' => $e->prenom,
                    'alias'  => $e->alias,
                    'lien'   => $e->getLien(),
                    'qrCode' => $e->getQrCode(),
                );
            }
        }
        return $res;
    }

    public function countMatin($deb, $fin)
    {
        return $this->countRepas($deb, $fin, 'petitDej');
    }

    public function countMidi($deb, $fin)
    {
        return $this->countRepas($deb, $fin, 'dejeuner');
    }

    public function countSoir($deb, $fin)
    {
        return $this->countRepas($deb, $fin, 'gouter');
    }
}

class_alias(enfantModel::class, 'enfantModel');
