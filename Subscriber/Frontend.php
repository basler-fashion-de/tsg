<?php

namespace BlaubandOneClickSystem\Subscriber;

use BlaubandOneClickSystem\Services\ConfigService;
use Enlight\Event\SubscriberInterface;

class Frontend implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onFrontendPostDispatch'
        );
    }

    public function onFrontendPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        $this->checkSearchEngineRobot($view);

        $view->addTemplateDir(Shopware()->Container()->getParameter('blauband_one_click_system.plugin_dir') . '/Resources/views');
    }

    /**
     * Schaut in der Config.php ob Suchmaschinen diese Seite indezieren dürfen
     *
     * @param $view
     */
    private function checkSearchEngineRobot($view){
        /** @var ConfigService $config */
        $config = Shopware()->Container()->get('blauband_one_click_system.config_php_service');
        $view->assign('blaubandOcsIsGuest', $config->get('blauband.ocs.isGuest'));

        if($config->get('blauband.ocs.noIndex') == 'true'){
            header("X-Robots-Tag: noindex, nofollow", true);
        }


    }
}
