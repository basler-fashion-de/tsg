<?php

namespace BlaubandTSG\Tests;

use BlaubandTSG\BlaubandTSG as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'BlaubandTSG' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['BlaubandTSG'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
