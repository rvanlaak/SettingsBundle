<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Tests;

use Dmishh\Bundle\SettingsBundle\Manager\SettingsManager;
use Dmishh\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Dmishh\Bundle\SettingsBundle\Serializer\SerializerFactory;
use Mockery;

class SettingsManagerTest extends AbstractTest
{
    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException
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
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_USER', $user);
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_setting', $user));
    }

    public function testUserSettingsClear()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL', $user);
        $settingsManager->clear('some_setting', $user);
        $this->assertNull($settingsManager->get('some_setting', $user));
    }

    public function testGlobalAndUserSettingsArentIntersect()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_GLOBAL');
        $settingsManager->set('some_setting', 'VALUE_USER', $user);
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_setting'));
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_setting', $user));

        // in reverse order
        $settingsManager->set('some_setting', 'VALUE_USER_2', $user);
        $settingsManager->set('some_setting', 'VALUE_GLOBAL_2');
        $this->assertEquals('VALUE_GLOBAL_2', $settingsManager->get('some_setting'));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $user));
    }

    public function testUsersSettingsArentIntersect()
    {
        $user1 = $this->createUser(1);
        $user2 = $this->createUser(2);
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_setting', 'VALUE_USER_1', $user1);
        $settingsManager->set('some_setting', 'VALUE_USER_2', $user2);
        $this->assertEquals('VALUE_USER_1', $settingsManager->get('some_setting', $user1));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $user2));

        // in reverse order
        $settingsManager->set('some_setting', 'VALUE_USER_2', $user2);
        $settingsManager->set('some_setting', 'VALUE_USER_1', $user1);
        $this->assertEquals('VALUE_USER_1', $settingsManager->get('some_setting', $user1));
        $this->assertEquals('VALUE_USER_2', $settingsManager->get('some_setting', $user2));
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
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    public function testSetUserSettingInGlobalScopeRaisesException()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_global_setting', 'VALUE_GLOBAL');
        $this->assertEquals('VALUE_GLOBAL', $settingsManager->get('some_global_setting'));

        $settingsManager->set('some_global_setting', 'VALUE_GLOBAL', $user);
    }

    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    public function testGetUserSettingInGlobalScopeRaisesException()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->get('some_global_setting', $user);
    }

    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    public function testSetGlobalSettingInUserScopeRaisesException()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $settingsManager->set('some_user_setting', 'VALUE_USER', $user);
        $this->assertEquals('VALUE_USER', $settingsManager->get('some_global_setting', $user));

        $settingsManager->set('some_user_setting', 'VALUE_USER');
    }

    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    public function testGetGlobalSettingInUserScopeRaisesException()
    {
        $settingsManager = $this->createSettingsManager();
        $settingsManager->get('some_user_setting');
    }

    public function testGetAllGlobalSettings()
    {
        $settingsManager = $this->createSettingsManager();
        $this->assertEquals(array('some_setting' => null, 'some_setting2' => null, 'some_global_setting' => null), $settingsManager->all());
    }

    public function testGetAllUserSettings()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();
        $this->assertEquals(array('some_setting' => null, 'some_setting2' => null, 'some_user_setting' => null), $settingsManager->all($user));
    }

    public function testScopeAll()
    {
        $user = $this->createUser();
        $settingsManager = $this->createSettingsManager();

        // Global settings should be shown if there is no user setting defined
        $settingsManager->set('some_setting', 'value');
        $this->assertEquals('value', $settingsManager->get('some_setting'));
        $this->assertEquals('value', $settingsManager->get('some_setting', $user), 'Did not get global value when local value was undefined.');

        // The users settings should always be prioritised over the global one (if it exists)
        $settingsManager->set('some_setting', 'user_value', $user);
        $this->assertEquals('user_value', $settingsManager->get('some_setting', $user), 'User/Local value should have priority over global.');
        $this->assertEquals('value', $settingsManager->get('some_setting'));
    }

    public function testValidSerizalizationTypes()
    {
        $settingsManager = $this->createSettingsManager(array(), 'php');
        $settingsManager->set('some_setting', 123);
        $this->assertEquals(123, $settingsManager->get('some_setting'));

        $settingsManager = $this->createSettingsManager(array(), 'json');
        $settingsManager->set('some_setting', 123);
        $this->assertEquals(123, $settingsManager->get('some_setting'));
    }

    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\SettingsException
     */
    public function testSetSettingWithInvalidSerizalizationType()
    {
        $settingsManager = $this->createSettingsManager(array(), 'unknown_serialization_type');
        $settingsManager->set('some_setting', 123);
    }

    /**
     * @expectedException \Dmishh\Bundle\SettingsBundle\Exception\SettingsException
     */
    public function testGetSettingWithInvalidSerizalizationType()
    {
        $settingsManager = $this->createSettingsManager(array());
        $settingsManager->set('some_setting', 123);

        $settingsManager = $this->createSettingsManager(array(), 'unknown_serialization_type');
        $settingsManager->get('some_setting');
    }

    /**
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    protected function createUser($username = 'user1')
    {
        return Mockery::mock('Symfony\Component\Security\Core\User\UserInterface', array('getUsername' => $username));
    }

    protected function createSettingsManager(array $configuration = array(), $serialization = 'php')
    {
        if (empty($configuration)) {
            $configuration = array(
                'some_setting' => array('scope' => SettingsManagerInterface::SCOPE_ALL),
                'some_setting2' => array('scope' => SettingsManagerInterface::SCOPE_ALL),
                'some_global_setting' => array('scope' => SettingsManagerInterface::SCOPE_GLOBAL),
                'some_user_setting' => array('scope' => SettingsManagerInterface::SCOPE_USER),
            );
        }

        $serializer = SerializerFactory::create($serialization);
        return new SettingsManager($this->em, $configuration, $serializer);
    }
}
