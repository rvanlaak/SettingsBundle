<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\Manager\SettingsManager;
use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Dmishh\SettingsBundle\Serializer\SerializerFactory;
use Mockery;

class SettingsManagerTest extends AbstractTest
{
    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\UnknownSettingException
     */
    public function testGetUnknownSettingShouldRaiseException()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->get('unknown_setting');
    }

    public function testGlobalSettingsAccessor()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL');
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_setting'));
    }

    public function testGlobalSettingsClear()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL');
        $settingsManager->clear('some_setting');
        $this->assertNull($settingsManager->get('some_setting'));
    }

    public function testUserSettingsAccessor()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_USER', $owner);
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_setting', $owner));
    }

    public function testUserSettingsClear()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL', $owner);
        $settingsManager->clear('some_setting', $owner);
        $this->assertNull($settingsManager->get('some_setting', $owner));
    }

    public function testGlobalAndUserSettingsArentIntersect()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL');
        $settingsManager->set('some_setting', 'VALUE_USER', $owner);
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_setting'));
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_setting', $owner));

        // in reverse order
        $settingsManager->set('some_setting', 'VALUE_USER_2', $owner);
        $settingsManager->set('some_setting', 'VALUE_GLOBAL_2');
        $this->assertEquals('VALUE_GLOBAL_2', $settingsManager->get('some_setting'));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $owner));
    }

    public function testUsersSettingsArentIntersect()
    {
        $owner1 = $this->createOwner(1);
        $owner2 = $this->createOwner(2);
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_USER_1', $owner1);
        $settingsManager->set('some_setting', 'VALUE_USER_2', $owner2);
        $this->assertEquals('VALUE_USER_1', $settingsManager->get('some_setting', $owner1));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $owner2));

        // in reverse order
        $settingsManager->set('some_setting', 'VALUE_USER_2', $owner2);
        $settingsManager->set('some_setting', 'VALUE_USER_1', $owner1);
        $this->assertEquals('VALUE_USER_1', $settingsManager->get('some_setting', $owner1));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $owner2));
    }

    public function testPersistence()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL');

        // creating new manager and cheking for some_setting
        $settingsManager = $this->createSettingsManager();
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_setting'));

        // clear
        $settingsManager->clear('some_setting');
        $settingsManager = $this->createSettingsManager();
        $this->assertNull($settingsManager->get('some_setting'));
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\WrongScopeException
     */
    public function testSetUserSettingInGlobalScopeRaisesException()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_global_setting', 'VALUE_GLOBAL');
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_global_setting'));

        $settingsManager->set('some_global_setting', 'VALUE_GLOBAL', $owner);
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\WrongScopeException
     */
    public function testGetUserSettingInGlobalScopeRaisesException()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->get('some_global_setting', $owner);
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\WrongScopeException
     */
    public function testSetGlobalSettingInUserScopeRaisesException()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_user_setting', 'VALUE_USER', $owner);
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_global_setting', $owner));

        $settingsManager->set('some_user_setting', 'VALUE_USER');
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\WrongScopeException
     */
    public function testGetGlobalSettingInUserScopeRaisesException()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->get('some_user_setting');
    }

    public function testGetAllGlobalSettings()
    {
        $settingsManager = $this->createSettingsManager();
        $this->assertEquals(
            ['some_setting' => null, 'some_setting2' => null, 'some_global_setting' => null],
            $settingsManager->all()
        );
    }

    public function testGetAllUserSettings()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();
        $this->assertEquals(
            ['some_setting' => null, 'some_setting2' => null, 'some_user_setting' => null],
            $settingsManager->all($owner)
        );
    }

    public function testScopeAll()
    {
        $owner = $this->createOwner();
        $settingsManager = $this->createSettingsManager();

        // Global settings should be shown if there is no user setting defined
        $settingsManager->set('some_setting', 'value');
        $this->assertEquals('value', $settingsManager->get('some_setting'));
        $this->assertEquals(
            'value',
            $settingsManager->get('some_setting', $owner),
            'Did not get global value when local value was undefined.'
        );
        $this->assertEquals(
            ['some_setting' => 'value', 'some_setting2' => null, 'some_user_setting' => null],
            $settingsManager->all($owner),
            'Did not get global value when local value was undefined.'
        );

        // The users settings should always be prioritised over the global one (if it exists)
        $settingsManager->set('some_setting', 'user_value', $owner);
        $this->assertEquals(
            'user_value',
            $settingsManager->get('some_setting', $owner),
            'User/Local value should have priority over global.'
        );
        $this->assertEquals('value', $settingsManager->get('some_setting'));
        $this->assertEquals(
            ['some_setting' => 'user_value', 'some_setting2' => null, 'some_user_setting' => null],
            $settingsManager->all($owner),
            'User/Local value should have priority over global.'
        );
    }

    public function testValidSerizalizationTypes()
    {
        $settingsManager = $this->createSettingsManager([], 'php');
        $settingsManager->set('some_setting', 123);
        $this->assertEquals(123, $settingsManager->get('some_setting'));

        $settingsManager = $this->createSettingsManager([], 'json');
        $settingsManager->set('some_setting', 123);
        $this->assertEquals(123, $settingsManager->get('some_setting'));
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\SettingsException
     */
    public function testSetSettingWithInvalidSerizalizationType()
    {
        $settingsManager = $this->createSettingsManager([], 'unknown_serialization_type');
        $settingsManager->set('some_setting', 123);
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\SettingsException
     */
    public function testGetSettingWithInvalidSerizalizationType()
    {
        $settingsManager = $this->createSettingsManager([]);
        $settingsManager->set('some_setting', 123);

        $settingsManager = $this->createSettingsManager([], 'unknown_serialization_type');
        $settingsManager->get('some_setting');
    }

    public function testGetDefaultValue()
    {
        $user = $this->createOwner();
        $settingsManager = $this->createSettingsManager();

        //test default global value
        $this->assertNull($settingsManager->get('some_setting'));
        $this->assertEquals('foobar', $settingsManager->get('some_setting', null, 'foobar'));

        //test default user value
        $this->assertNull($settingsManager->get('some_setting'));
        $this->assertEquals('foobar', $settingsManager->get('some_setting', $user, 'foobar'));

        //test when there is an actual value
        $settingsManager->set('some_setting', 'value');
        $this->assertEquals('value', $settingsManager->get('some_setting', null, 'foobar'));
        $this->assertEquals('value', $settingsManager->get('some_setting', $user, 'foobar'));
    }

    /**
     * @see https://github.com/dmishh/SettingsBundle/issues/28
     */
    public function testFlush()
    {
        $names = ['foo', 'bar', 'baz'];
        $settings = 'foobar';
        $owner = null;
        $value = 'settingValue';
        $serializedValue = 'sValue';

        $flushMethod = new \ReflectionMethod('Dmishh\SettingsBundle\Manager\SettingsManager', 'flush');
        $flushMethod->setAccessible(true);

        $serializer = $this
            ->getMockBuilder('Dmishh\SettingsBundle\Serializer\PhpSerializer')
            ->setMethods(['serialize'])
            ->getMock();

        $serializer
            ->expects($this->exactly(\count($names)))
            ->method('serialize')
            ->with($this->equalTo($value))
            ->willReturn($serializedValue);

        $repo = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findBy'])
            ->getMock();

        $repo->expects($this->once())->method('findBy')->with(
            $this->equalTo(
                [
                    'name' => $names,
                    'ownerId' => $owner,
                ]
            )
        )->willReturn($settings);

        $em = $this
            ->getMockBuilder('Doctrine\Orm\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'flush'])
            ->getMock();

        $em->expects($this->once())->method('getRepository')->willReturn($repo);
        $em->expects($this->once())->method('flush');

        $setting = $this
            ->getMockBuilder('Dmishh\SettingsBundle\Entity\Settings')
            ->disableOriginalConstructor()
            ->setMethods(['setValue'])
            ->getMock();

        $setting->expects($this->exactly(\count($names)))->method('setValue')->with($this->equalTo($serializedValue));

        $manager = $this
            ->getMockBuilder('Dmishh\SettingsBundle\Manager\SettingsManager')
            ->setConstructorArgs([$em, $serializer, []])
            ->setMethods(['findSettingByName', 'get'])
            ->getMock();

        $manager
            ->expects($this->exactly(\count($names)))
            ->method('get')
            ->withConsecutive(
                [$this->equalTo('foo'), $owner],
                [$this->equalTo('bar'), $owner],
                [$this->equalTo('baz'), $owner]
            )
            ->willReturn($value);

        $manager
            ->expects($this->exactly(\count($names)))
            ->method('findSettingByName')
            ->withConsecutive(
                [$settings, $this->equalTo('foo')],
                [$settings, $this->equalTo('bar')],
                [$settings, $this->equalTo('baz')]
            )->willReturn($setting);

        $flushMethod->invoke($manager, $names, $owner);
    }

    public function testFindSettingByName()
    {
        $settingsManager = $this->createSettingsManager();

        $s1 = $this->createSetting('foo');
        $s2 = $this->createSetting('bar');
        $s3 = $this->createSetting('baz');
        $s4 = $this->createSetting('foo');
        $settings = [$s1, $s2, $s3, $s4];

        $method = new \ReflectionMethod('Dmishh\SettingsBundle\Manager\SettingsManager', 'findSettingByName');
        $method->setAccessible(true);

        $result = $method->invoke($settingsManager, $settings, 'bar');
        $this->assertEquals($s2, $result);

        $result = $method->invoke($settingsManager, $settings, 'biz');
        $this->assertNull($result);

        $result = $method->invoke($settingsManager, $settings, 'foo');
        $this->assertEquals($s1, $result);
    }

    protected function createSetting($name)
    {
        $s = $this->getMockBuilder('Dmishh\SettingsBundle\Entity\Setting')
            ->setMethods(['getName'])
            ->getMock();

        $s->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $s;
    }

    /**
     * @param string $ownerId
     *
     * @return \Dmishh\SettingsBundle\Entity\SettingsOwnerInterface
     */
    protected function createOwner($ownerId = 'user1')
    {
        return Mockery::mock(
            'Dmishh\SettingsBundle\Entity\SettingsOwnerInterface',
            ['getSettingIdentifier' => $ownerId]
        );
    }

    protected function createSettingsManager(array $configuration = [], $serialization = 'php')
    {
        if (empty($configuration)) {
            $configuration = [
                'some_setting' => ['scope' => SettingsManagerInterface::SCOPE_ALL],
                'some_setting2' => ['scope' => SettingsManagerInterface::SCOPE_ALL],
                'some_global_setting' => ['scope' => SettingsManagerInterface::SCOPE_GLOBAL],
                'some_user_setting' => ['scope' => SettingsManagerInterface::SCOPE_USER],
            ];
        }

        $serializer = SerializerFactory::create($serialization);

        return new SettingsManager($this->em, $serializer, $configuration);
    }
}
