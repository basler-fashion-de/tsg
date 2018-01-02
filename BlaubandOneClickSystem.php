<?php

namespace BlaubandOneClickSystem;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Shopware-Plugin BlaubandOneClickSystem.
 */
class BlaubandOneClickSystem extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('blauband_one_click_system.plugin_dir', $this->getPath());
        parent::build($container);
    }

}
