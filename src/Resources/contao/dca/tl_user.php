<?php

use trdev\ContaoCantineBundle\Classes\TypeChamp;

$palettes = [
    'login',
    'admin',
    'default',
    'group',
    'extend',
    'custom',
];

foreach ($palettes as $key) {
    $GLOBALS['TL_DCA']['tl_user']['palettes'][$key] = str_replace(
        'email',
        'email,etablissement',
        $GLOBALS['TL_DCA']['tl_user']['palettes'][$key]
    );
}

$GLOBALS['TL_DCA']['tl_user']['fields']['etablissement'] = TypeChamp::selectTable('tl_etablissement.nom', false, true, false);
