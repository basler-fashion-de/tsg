<?php

namespace BlaubandOneClickSystem;

use BlaubandOneClickSystem\Installers\CronJob;
use BlaubandOneClickSystem\Installers\Mails;
use BlaubandOneClickSystem\Services\ConfigService;
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

        (new CronJob(
            $this->container->get('dbal_connection'),
            $this->getPath()
        ))->fixCronTab();
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

            '1.0.2' => function () {
                (new Mails(
                    $this->container->get('models'),
                    new ConfigService($this->getPath().'/Resources/mails.xml'),
                    $this->getPath()
                ))->install();
                return true;
            },

            '1.0.3' => function () {
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
