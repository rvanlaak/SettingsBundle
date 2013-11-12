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
use Mockery;

class SettingsManagerTest extends AbstractTest
{
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
     * @param int $id
     * @return \Dmishh\Bundle\SettingsBundle\Entity\UserInterface
     */
    protected function createUser($id = 1)
    {
        return Mockery::mock('Dmishh\Bundle\SettingsBundle\Entity\UserInterface', array('getId' => $id));
    }

    protected function createSettingsManager(array $options = array())
    {
        return new SettingsManager($this->em, !empty($options) ? $options : array('setting_names' => array('some_setting')));
    }
}
