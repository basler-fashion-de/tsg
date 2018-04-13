<?php

namespace BlaubandTSG\Subscriber;

use BlaubandTSG\Services\ConfigService;
use Enlight\Event\SubscriberInterface;

class Backend implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'onBackendPostDispatch'
        );
    }

    public function onBackendPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        /** @var ConfigService $config */
        $config = Shopware()->Container()->get('blauband_tsg.config_php_service');
        $view->assign('blaubandTsgIsGuest', $config->get('blauband.tsg.isGuest'));

        $view->addTemplateDir(Shopware()->Container()->getParameter('blauband_tsg.plugin_dir') . '/Resources/views');
        $view->extendsTemplate('backend/blauband_tsg/icons.tpl');
    }
}
