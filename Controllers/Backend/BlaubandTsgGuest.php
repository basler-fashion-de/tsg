<?php

use Shopware\Components\CSRFWhitelistAware;
use BlaubandTSG\Controllers\Backend\BlaubandEnlightControllerAction;

class Shopware_Controllers_Backend_BlaubandTSGGuest extends BlaubandEnlightControllerAction implements CSRFWhitelistAware
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
        /** @var \BlaubandTSG\Services\System\Local\MailService $mailService */
        $mailService = $this->container->get('blauband_tsg.mail_service');
        $this->View()->assign('mails', $mailService->loadMails());
        $this->View()->assign('mailsAllow', $mailService->isMailAllowed($this->container->getParameter('kernel.root_dir')));

    }

    public function allowMailAction()
    {
        $allow = $this->Request()->getParam('allow') == 'radio-yes';
        $dorRoot = $this->container->getParameter('kernel.root_dir');

        /** @var \BlaubandTSG\Services\System\Local\MailService $mailService */
        $mailService = $this->container->get('blauband_tsg.mail_service');

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