<?php

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Backend_BlaubandOneClickSystem extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        return [
            'index'
        ];
    }

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $pluginPath = $this->container->getParameter('blauband_one_click_system.plugin_dir');
        $this->View()->addTemplateDir($pluginPath . '/Resources/views/');
    }

    /**
     * Post dispatch method
     */
    public function postDispatch()
    {
        $pluginPath = $this->container->getParameter('blauband_one_click_system.plugin_dir');
        $this->View()->assign('publicFilePath', $pluginPath . '/Resources/views/backend/_public/');
    }

    /**
     * Startseite
     */
    public function indexAction()
    {
        $this->View()->assign("systems", [
            ['name' => 'Test-System 1', 'createDate' => '1.1.2018'],
            ['name' => 'Test-System 2', 'createDate' => '2.1.2018']
        ]);
    }

    /**
     *  Ajax aufruf um System zu erstellen
     */
    public function createSystemAction()
    {

    }
}