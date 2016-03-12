<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\DependencyInjection\DmishhSettingsExtension;
use Dmishh\SettingsBundle\Serializer\JsonSerializer;
use Dmishh\SettingsBundle\Serializer\PhpSerializer;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ServiceTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new DmishhSettingsExtension(),
        );
    }

    public function testAlias()
    {
        $this->load();
        $this->assertContainerBuilderHasAlias('settings_manager', 'dmishh.settings.settings_manager');
    }

    /**
     * If we provide a cache_service we should use the CachedSettingsManager as default.
     */
    public function testCacheServiceAlias()
    {
        $this->load(array('cache_service' => 'cache'));
        $this->assertContainerBuilderHasAlias('settings_manager', 'dmishh.settings.cached_settings_manager');
    }
}
