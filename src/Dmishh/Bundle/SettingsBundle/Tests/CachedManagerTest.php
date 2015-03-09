<?php

namespace Dmishh\Bundle\SettingsBundle\Tests;

use Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\Bundle\SettingsBundle\Manager\CachedManager;
use Mockery as m;

class CachedManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');
        $sm->shouldReceive('get')->once()->with($name, $owner, $defaultValue)->andReturn($value);

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);
        $manager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo($name), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(null);

        $this->assertEquals($value, $manager->get($name, $owner, $defaultValue));
    }

    public function testGetHit()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';
        $defaultValue = 'default';

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn($value);

        $this->assertEquals($value, $manager->get($name, $owner, $defaultValue));
    }

    public function testAll()
    {
        $owner = $this->createOwner();
        $value = array('foo' => 'bar');

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');
        $sm->shouldReceive('all')->once()->with($owner)->andReturn($value);

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn(null);
        $manager->expects($this->once())
            ->method('storeInCache')
            ->with($this->equalTo(null), $this->equalTo($value), $this->equalTo($owner))
            ->willReturn(null);

        $this->assertEquals($value, $manager->all($owner));
    }

    public function testAllHit()
    {
        $owner = $this->createOwner();
        $value = array('foo' => 'bar');

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('fetchFromCache', 'storeInCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('fetchFromCache')
            ->with($this->equalTo(null), $this->equalTo($owner))
            ->willReturn($value);

        $this->assertEquals($value, $manager->all($owner));
    }

    public function testSet()
    {
        $owner = $this->createOwner();
        $name = 'name';
        $value = 'foobar';

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');
        $sm->shouldReceive('set')->once()->with($name, $value, $owner);

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);

        $manager->set($name, $value, $owner);
    }

    public function testSetMany()
    {
        $owner = $this->createOwner();
        $settings = array('name0' => 'value0', 'name1' => 'value1', 'name2' => 'value2');

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');
        $sm->shouldReceive('setMany')->once()->with($settings, $owner);

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->exactly(3))
            ->method('invalidateCache')
            ->with($this->logicalOr('name0', 'name1', 'name2'), $owner);

        $manager->setMany($settings, $owner);
    }

    public function testClear()
    {
        $owner = $this->createOwner();
        $name = 'name';

        $sm = m::mock('Dmishh\Bundle\SettingsBundle\Manager\SettingsManager');
        $sm->shouldReceive('clear')->once()->with($name, $owner);

        $manager = $this->getMockBuilder('Dmishh\Bundle\SettingsBundle\Manager\CachedManager')
            ->setMethods(array('invalidateCache'))
            ->setConstructorArgs(array($sm, 4711))
            ->getMock();
        $manager->expects($this->once())
            ->method('invalidateCache')
            ->with($this->equalTo($name), $this->equalTo($owner))
            ->willReturn(null);

        $manager->clear($name, $owner);
    }

    public function testGetCacheKey()
    {
        $name = 'name';
        $owner = $this->createOwner();

        $manager = new CachedDummy();

        $this->assertTrue(is_string($manager->getCacheKey($name, $owner)));
        $this->assertTrue(is_string($manager->getCacheKey($name, null)));
        $this->assertTrue(is_string($manager->getCacheKey(null, $owner)));
        $this->assertTrue(is_string($manager->getCacheKey(null, null)));
    }

    /**
     * @param string $ownerId
     *
     * @return \Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface
     */
    protected function createOwner($ownerId = 'user1')
    {
        return m::mock('Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface', array('getSettingIdentifier' => $ownerId));
    }
}

class CachedDummy extends CachedManager
{
    public function __construct()
    {
    }

    public function getCacheKey($key, SettingsOwnerInterface $owner = null)
    {
        return parent::getCacheKey($key, $owner);
    }
}
