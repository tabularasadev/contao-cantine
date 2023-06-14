<?php

namespace trdev\ContaoCantineBundle\Model;

class repasModel extends \Model
{
    protected static $strTable = 'tl_repas';

}

class_alias(repasModel::class, 'repasModel');
