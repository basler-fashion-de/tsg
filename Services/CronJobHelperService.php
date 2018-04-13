<?php

namespace BlaubandTSG\Services;

class CronJobHelperService
{
    /** @var string */
    private $cronJobName;

    /** @var string */
    private $rootPath;

    public function __construct($cronJobConfig, $rootPath)
    {
        $this->cronJobName = $cronJobConfig->get('cronjob.action');
        $this->rootPath = $rootPath;
    }

    public function fire(){
        $command = 'nohup php '.$this->rootPath.'/bin/console sw:cron:run '.$this->cronJobName.' > nohup.out & > /dev/null';
        exec($command);
    }
}