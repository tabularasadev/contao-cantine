<?php

declare (strict_types = 1);

namespace trdev\ContaoCantineBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use trdev\ContaoCantineBundle\ContaoCantineBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoCantineBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
