<?php

namespace BlaubandTSG;

use BlaubandTSG\Installers\Api;
use BlaubandTSG\Installers\CronJob;
use BlaubandTSG\Installers\Mails;
use BlaubandTSG\Services\ConfigService;
use BlaubandTSG\Services\System\Common\DBConnectionService;
use BlaubandTSG\Services\System\Common\DBDuplicationService;
use BlaubandTSG\Services\System\Common\TSGApiService;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BlaubandTSG\Installers\Models;

/**
 * Shopware-Plugin BlaubandTSG.
 */
class BlaubandTSG extends Plugin
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('blauband_tsg.plugin_dir', $this->getPath());
        parent::build($container);
    }

    public function install(InstallContext $context)
    {
        $this->setup(null, $context->getCurrentVersion());
        parent::install($context);

        (new Api($this->getTsgService()))->install();

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

        (new Api($this->getTsgService()))->uninstall();

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

            '1.0.3' => function () {
                (new Models($this->container->get('models')))->update();
                return true;
            },

            '1.0.4' => function () {
                (new Models($this->container->get('models')))->update();
                return true;
            },

            //Next Release
//            '1.0.x' => function () {
//                (new Mails(
//                    $this->container->get('models'),
//                    new ConfigService($this->getPath() . '/Resources/mails.xml'),
//                    $this->getPath()
//                ))->install();
//                return true;
//            },


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

    private function getTsgService()
    {
        return new TSGApiService(
            $this->container->get('snippets'),
            new DBConnectionService(
                $this->container->get('snippets'),
                new DBDuplicationService(
                    $this->container->get('snippets'),
                    $this->container->get('pluginlogger'),
                    $this->getPath()
                )
            ),
            $this->container->get('dbal_connection'),
            $this->getPath() . '/Resources/token.lock'
        );
    }
}
