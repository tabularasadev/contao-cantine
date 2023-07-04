<?php

use trdev\ContaoCantineBundle\Classes\TypeChamp;

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{APE},objetMailFacture,messageMailFacture';

$GLOBALS['TL_DCA']['tl_settings']['fields']['objetMailFacture']   = TypeChamp::text(true);
$GLOBALS['TL_DCA']['tl_settings']['fields']['messageMailFacture'] = TypeChamp::textarea(true, true);
$GLOBALS['TL_DCA']['tl_settings']['fields']['objetMailRelance']   = TypeChamp::text(true);
$GLOBALS['TL_DCA']['tl_settings']['fields']['messageMailRelance'] = TypeChamp::textarea(true, true);
