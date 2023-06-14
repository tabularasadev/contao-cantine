<?php

namespace trdev\ContaoCantineBundle\Model;

class etablissementModel extends \Model
{
    protected static $strTable = 'tl_etablissement';

}

class_alias(etablissementModel::class, 'etablissementModel');
