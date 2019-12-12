<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\Manager\CachedSettingsManager;
use Dmishh\SettingsBundle\Manager\SettingsManager;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class CachedSettingsManagerTest extends TestCase
{
    public function testGet()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $settingsManager = \Mockery::mock(SettingsManager::class);
        $settingsManager->shouldReceive('get')->once()->with($name, $owner, $defaultValue)->andReturn($value);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['fetchFromCache', 'storeInCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);
        $cachedSettingsManager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo($name), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(false);

        $this->assertEquals($value, $cachedSettingsManager->get($name, $owner, $defaultValue));
    }

    public function testGetHit()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $settingsManager = \Mockery::mock(SettingsManager::class);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['fetchFromCache', 'storeInCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn($value);

        $this->assertEquals($value, $cachedSettingsManager->get($name, $owner, $defaultValue));
    }

    public function testAll()
    {
        $owner = $this->createOwner();
        $value = ['foo' => 'bar'];

        $settingsManager = \Mockery::mock(SettingsManager::class);
        $settingsManager->shouldReceive('all')->once()->with($owner)->andReturn($value);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['fetchFromCache', 'storeInCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn(null);
        $cachedSettingsManager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo(null), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(false);

        $this->assertEquals($value, $cachedSettingsManager->all($owner));
    }

    public function testAllHit()
    {
        $owner = $this->createOwner();
        $value = ['foo' => 'bar'];

        $settingsManager = \Mockery::mock(SettingsManager::class);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['fetchFromCache', 'storeInCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn($value);

        $this->assertEquals($value, $cachedSettingsManager->all($owner));
    }

    public function testSet()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';

        $settingsManager = \Mockery::mock(SettingsManager::class);
        $settingsManager->shouldReceive('set')->once()->with($name, $value, $owner);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['invalidateCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();

        // Clear the cache
        $cachedSettingsManager->expects($this->at(0))
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);

        // Clear all cache for this owner
        $cachedSettingsManager->expects($this->at(1))
            ->method('invalidateCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn(null);

        $cachedSettingsManager->set($name, $value, $owner);
    }

    public function testSetMany()
    {
        $owner = $this->createOwner();
        $settings = ['name0' => 'value0', 'name1' => 'value1', 'name2' => 'value2'];

        $settingsManager = \Mockery::mock(SettingsManager::class);
        $settingsManager->shouldReceive('setMany')->once()->with($settings, $owner);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['invalidateCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->exactly(4))
            ->method('invalidateCache')
            ->with($this->logicalOr('name0', 'name1', 'name2', null), $owner);

        $cachedSettingsManager->setMany($settings, $owner);
    }

    public function testClear()
    {
        $owner = $this->createOwner();
        $name = 'name';

        $settingsManager = \Mockery::mock(SettingsManager::class);
        $settingsManager->shouldReceive('clear')->once()->with($name, $owner);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->setMethods(['invalidateCache'])
            ->setConstructorArgs([$settingsManager, $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(), 4711])
            ->getMock();
        $cachedSettingsManager->expects($this->at(0))
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);
        $cachedSettingsManager->expects($this->at(1))
            ->method('invalidateCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn(null);

        $cachedSettingsManager->clear($name, $owner);
    }

    /**
     * Make sure we do always return a string, no matter input.
     */
    public function testGetCacheKey()
    {
        $name = 'name';
        $owner = $this->createOwner();

        $getCacheKey = new \ReflectionMethod(CachedSettingsManager::class, 'getCacheKey');
        $getCacheKey->setAccessible(true);

        $cachedSettingsManager = $this->getMockBuilder(CachedSettingsManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertStringContainsString('dmishh_settings', $getCacheKey->invoke($cachedSettingsManager, $name, $owner));
        $this->assertStringContainsString('dmishh_settings', $getCacheKey->invoke($cachedSettingsManager, $name, null));
        $this->assertStringContainsString('dmishh_settings', $getCacheKey->invoke($cachedSettingsManager, null, $owner));
        $this->assertStringContainsString('dmishh_settings', $getCacheKey->invoke($cachedSettingsManager, null, null));
    }

    /**
     * @param string $ownerId
     *
     * @return \Dmishh\SettingsBundle\Entity\SettingsOwnerInterface
     */
    protected function createOwner($ownerId = 'user1')
    {
        return \Mockery::mock(
            'Dmishh\SettingsBundle\Entity\SettingsOwnerInterface',
            ['getSettingIdentifier' => $ownerId]
        );
    }
}
