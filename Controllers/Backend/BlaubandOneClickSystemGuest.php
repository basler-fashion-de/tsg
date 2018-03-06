<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandOneClickSystem\Services\System\SystemService;
use BlaubandOneClickSystem\Services\System\SystemServiceInterface;
use Shopware\Components\Model\ModelManager;
use BlaubandOneClickSystem\Models\System;
use BlaubandOneClickSystem\Services\System\Local\SystemValidation;
use BlaubandOneClickSystem\Services\ConfigService;
use BlaubandOneClickSystem\Controllers\Backend\BlaubandEnlightControllerAction;
use BlaubandOneClickSystem\Services\System\Local\SetUpSystemService;

class Shopware_Controllers_Backend_BlaubandOneClickSystemGuest extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
            'allowMail'
        ];
    }

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        parent::preDispatch();
    }

    /**
     * Startseite
     */
    public function indexAction()
    {
        /** @var \BlaubandOneClickSystem\Services\System\Local\MailService $mailService */
        $mailService = $this->container->get('blauband_one_click_system.mail_service');
        $this->View()->assign('mails', $mailService->loadMails());
        $this->View()->assign('mailsAllow', $mailService->isMailAllowed($this->container->getParameter('kernel.root_dir')));

    }

    public function allowMailAction()
    {
        $allow = $this->Request()->getParam('allow') == 'radio-yes';
        $dorRoot = $this->container->getParameter('kernel.root_dir');

        /** @var \BlaubandOneClickSystem\Services\System\Local\MailService $mailService */
        $mailService = $this->container->get('blauband_one_click_system.mail_service');

        try {
            if ($allow) {
                $mailService->allowMail($dorRoot);
            } else {
                $mailService->preventMail($dorRoot);
            }

            $this->sendJsonResponse(
                [
                    'success' => true,
                    'allow' => $allow,
                ]
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                [
                    'success' => false,
                    'allow' => $allow,
                    'error' => $e->getMessage()
                ]
            );
        }


    }
}