<?php

namespace BlaubandOneClickSystem\Subscriber;

use BlaubandOneClickSystem\Services\ConfigService;
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
        $config = Shopware()->Container()->get('blauband_one_click_system.config_php_service');
        $view->assign('blaubandOcsIsGuest', $config->get('blauband.ocs.isGuest'));

        $view->addTemplateDir(Shopware()->Container()->getParameter('blauband_one_click_system.plugin_dir') . '/Resources/views');
        $view->extendsTemplate('backend/blauband_one_click_system/icons.tpl');
    }
}
