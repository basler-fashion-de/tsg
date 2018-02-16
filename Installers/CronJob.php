<?php

namespace BlaubandOneClickSystem\Installers;

use BlaubandOneClickSystem\Services\ConfigService;
use Doctrine\DBAL\Connection;

class CronJob
{
    /**
     * @var Connection
     * */
    private $connection;

    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * Models constructor.
     *
     * @param Connection $connection
     */
    public function __construct(
        Connection $connection,
        $rootDoc
    )
    {
        $this->connection = $connection;
        $this->configService = new ConfigService($rootDoc.'/Resources/cronjob.xml');
    }

    public function fixCronTab()
    {
        $cronName = $this->configService->get('cronjob.action');
        $this->connection->update(
            's_crontab',
            ['start' => 'now()', 'end' => 'now()'],
            ['action' => $cronName]
        );
    }
}
