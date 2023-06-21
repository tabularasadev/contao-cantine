<?php

namespace trdev\ContaoCantineBundle\Module;

use trdev\ContaoCantineBundle\Classes\pdfGenerateur;
use trdev\ContaoCantineBundle\Model\enfantModel;

class beModuleCoupon extends \BackendModule
{

    protected $strTemplate = "beCoupon";

    protected function compile()
    {
        $test = new pdfGenerateur('coupon', 'Liste des enfants', enfantModel::getForCoupons());
    }
}
