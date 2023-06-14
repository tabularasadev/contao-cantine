<?php

use trdev\ContaoCantineBundle\Classes\TypeChamp;

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
    'gender',
    'gender;enfants',
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_member']['fields']['enfants'] = TypeChamp::selectTable('tl_enfant.CONCAT(prenom, " ", nom)', true, true, false);
