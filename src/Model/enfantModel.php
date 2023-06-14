<?php

namespace trdev\ContaoCantineBundle\Model;

use trdev\ContaoCantineBundle\Model\repasModel;

class enfantModel extends \Model
{
    protected static $strTable = 'tl_enfant';

    public function countRepas($dateDebut, $dateFin)
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
                if (isset($key->petitDej) && $key->petitDej == 'Oui') {
                    $i = $i + 1;
                }
                if (isset($key->dejeuner) && $key->dejeuner == "Oui") {
                    $i = $i + 1;
                }
                if (isset($key->gouter) && $key->gouter == "Oui") {
                    $i = $i + 1;
                }
            }
        }
        return $i;
    }
}

class_alias(enfantModel::class, 'enfantModel');
