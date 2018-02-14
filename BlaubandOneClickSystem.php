<?php

namespace BlaubandOneClickSystem;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BlaubandOneClickSystem\Installers\Models;
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

    public function install(InstallContext $context)
    {
        $this->setup(null, $context->getCurrentVersion());
        parent::install($context);
    }

    public function update(UpdateContext $context)
    {
        $this->setup($context->getCurrentVersion(), $context->getUpdateVersion());
        parent::update($context);
    }

    public function uninstall(UninstallContext $context)
    {
        if (!$context->keepUserData()) {
            (new Models($this->container->get('models')))->uninstall();
        }

        parent::uninstall($context);
    }

    /**
     * @param string|null $oldVersion
     * @param string|null $newVersion
     *
     * @return bool
     */
    public function setup($oldVersion = null, $newVersion = null)
    {
        $versions = [
            '1.0.0' => function () {
                (new Models($this->container->get('models')))->install();
                return true;
            },

            '1.0.1' => function () {
                (new Models($this->container->get('models')))->update();
                return true;
            },
        ];

        foreach ($versions as $version => $callback) {
            if ($oldVersion === null || (version_compare($oldVersion, $version, '<') && version_compare($version, $newVersion, '<='))) {
                if (!$callback($this)) {
                    return false;
                }
            }
        }

        return true;
    }
}
