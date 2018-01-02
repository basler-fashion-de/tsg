<?php

namespace BlaubandOneClickSystem\Tests;

use BlaubandOneClickSystem\BlaubandOneClickSystem as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'BlaubandOneClickSystem' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['BlaubandOneClickSystem'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
