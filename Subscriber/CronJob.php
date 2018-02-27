<?php

namespace BlaubandOneClickSystem\Subscriber;

use BlaubandOneClickSystem\Services\System\Local;
use Enlight\Event\SubscriberInterface;

class CronJob implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Shopware_CronJob_BlaubandOCS' => 'onBlaubandOCS'
        );
    }

    public function onBlaubandOCS(\Shopware_Components_Cron_CronJob $job)
    {
        /** @var Local $localService */
        $localService = Shopware()->Container()->get('blauband_one_click_system.local_system_service');
        $localService->executeCreateSystem();
        $localService->executeDeleteSystem();
    }
}