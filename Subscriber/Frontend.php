<?php

namespace BlaubandTSG\Subscriber;

use BlaubandTSG\Services\ConfigService;
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

        $view->addTemplateDir(Shopware()->Container()->getParameter('blauband_tsg.plugin_dir') . '/Resources/views');
    }

    /**
     * Schaut in der Config.php ob Suchmaschinen diese Seite indezieren dürfen
     *
     * @param $view
     */
    private function checkSearchEngineRobot($view){
        /** @var ConfigService $config */
        $config = Shopware()->Container()->get('blauband_tsg.config_php_service');
        $view->assign('blaubandTsgIsGuest', $config->get('blauband.tsg.isGuest'));

        if($config->get('blauband.tsg.noIndex') == 'true'){
            header("X-Robots-Tag: noindex, nofollow", true);
        }


    }
}
