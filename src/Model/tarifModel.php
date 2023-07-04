<?php

namespace trdev\ContaoCantineBundle\Model;

class tarifModel extends \Model
{
    protected static $strTable = 'tl_tarif';

}

class_alias(tarifModel::class, 'tarifModel');
