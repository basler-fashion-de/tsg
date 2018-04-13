<?php

namespace BlaubandTSG\Subscriber;

use BlaubandTSG\Services\System\Local;
use Enlight\Event\SubscriberInterface;

class CronJob implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Shopware_CronJob_BlaubandTSG' => 'onBlaubandTSG'
        );
    }

    public function onBlaubandTSG(\Shopware_Components_Cron_CronJob $job)
    {
        ini_set('max_execution_time', 3600); // 1 Stunde

        /** @var Local $localService */
        $localService = Shopware()->Container()->get('blauband_tsg.local_system_service');
        $localService->executeCreateSystem();
        $localService->executeDeleteSystem();
        $localService->executeDuplicateMediaFolder();

    }
}