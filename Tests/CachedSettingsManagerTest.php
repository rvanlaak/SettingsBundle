<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\SettingsBundle\Manager\CachedSettingsManager;

class CachedSettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $settingManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');
        $settingManager->shouldReceive('get')->once()->with($name, $owner, $defaultValue)->andReturn($value);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($settingManager, 4711))
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);
        $cachedSettingsManager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo($name), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(null);

        $this->assertEquals($value, $cachedSettingsManager->get($name, $owner, $defaultValue));
    }

    public function testGetHit()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
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
        $value = array('foo' => 'bar');

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');
        $settingsManager->shouldReceive('all')->once()->with($owner)->andReturn($value);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn(null);
        $cachedSettingsManager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo(null), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(null);

        $this->assertEquals($value, $cachedSettingsManager->all($owner));
    }

    public function testAllHit()
    {
        $owner = $this->createOwner();
        $value = array('foo' => 'bar');

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
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

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');
        $settingsManager->shouldReceive('set')->once()->with($name, $value, $owner);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);

        $cachedSettingsManager->set($name, $value, $owner);
    }

    public function testSetMany()
    {
        $owner = $this->createOwner();
        $settings = array('name0' => 'value0', 'name1' => 'value1', 'name2' => 'value2');

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');
        $settingsManager->shouldReceive('setMany')->once()->with($settings, $owner);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
            ->getMock();
        $cachedSettingsManager->expects($this->exactly(3))
            ->method('invalidateCache')
            ->with($this->logicalOr('name0', 'name1', 'name2'), $owner);

        $cachedSettingsManager->setMany($settings, $owner);
    }

    public function testClear()
    {
        $owner = $this->createOwner();
        $name = 'name';

        $settingsManager = \Mockery::mock('Dmishh\SettingsBundle\Manager\SettingsManager');
        $settingsManager->shouldReceive('clear')->once()->with($name, $owner);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($settingsManager, 4711))
            ->getMock();
        $cachedSettingsManager->expects($this->once())
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);

        $cachedSettingsManager->clear($name, $owner);
    }

    /**
     * Make sure we do always return a string, no matter input
     */
    public function testGetCacheKey()
    {
        $name = 'name';
        $owner = $this->createOwner();

        $getCacheKey = new \ReflectionMethod('Dmishh\SettingsBundle\Manager\CachedSettingsManager', 'getCacheKey');
        $getCacheKey->setAccessible(true);

        $cachedSettingsManager = $this->getMockBuilder('Dmishh\SettingsBundle\Manager\CachedSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        $getCacheKey->invoke($cachedSettingsManager, $name, $owner);
        $getCacheKey->invoke($cachedSettingsManager, $name, null);
        $getCacheKey->invoke($cachedSettingsManager, null, $owner);
        $getCacheKey->invoke($cachedSettingsManager, null, null);
    }

    /**
     * @param string $ownerId
     *
     * @return \Dmishh\SettingsBundle\Entity\SettingsOwnerInterface
     */
    protected function createOwner($ownerId = 'user1')
    {
        return \Mockery::mock('Dmishh\SettingsBundle\Entity\SettingsOwnerInterface', array('getSettingIdentifier' => $ownerId));
    }
}
