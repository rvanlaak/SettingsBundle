<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Manager;

use Dmishh\Bundle\SettingsBundle\Entity\Setting;
use Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException;
use Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException;
use Dmishh\Bundle\SettingsBundle\Serializer\SerializerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Settings Manager provides settings management and persistence using Doctrine's Object Manager
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 * @author Artem Zhuravlov
 */
class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var array
     */
    private $globalSettings;

    /**
     * @var array
     */
    private $userSettings;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $settingsConfiguration;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ObjectManager $em
     * @param array $settingsConfiguration
     * @param SerializerInterface $serializer
     */
    public function __construct(ObjectManager $em, array $settingsConfiguration = array(), SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('Dmishh\\Bundle\\SettingsBundle\\Entity\\Setting');
        $this->settingsConfiguration = $settingsConfiguration;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name, UserInterface $user = null)
    {
        $this->validateSetting($name, $user);
        $this->loadSettings($user);

        $value = null;

        switch ($this->settingsConfiguration[$name]['scope']) {
            case SettingsManagerInterface::SCOPE_GLOBAL:
                $value = $this->globalSettings[$name];
                break;
            case SettingsManagerInterface::SCOPE_ALL:
                $value = $this->globalSettings[$name];
                //Do not break here. Try to fetch the users settings
            case SettingsManagerInterface::SCOPE_USER:
                if ($user !== null) {
                    if ($this->userSettings[$user->getUsername()][$name] !== null) {
                        $value = $this->userSettings[$user->getUsername()][$name];
                    }
                }
                break;
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function all(UserInterface $user = null)
    {
        $this->loadSettings($user);

        if ($user === null) {
            return $this->globalSettings;
        } else {
            return $this->userSettings[$user->getUsername()];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($name, $value, UserInterface $user = null)
    {
        $this->setWithoutFlush($name, $value, $user);

        return $this->flush($name, $user);
    }

    /**
     * {@inheritDoc}
     */
    public function setMany(array $settings, UserInterface $user = null)
    {
        foreach ($settings as $name => $value) {
            $this->setWithoutFlush($name, $value, $user);
        }

        return $this->flush(array_keys($settings), $user);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($name, UserInterface $user = null)
    {
        return $this->set($name, null, $user);
    }

    /**
     * Sets setting value to private array. Used for settings' batch saving
     *
     * @param string $name
     * @param mixed $value
     * @param UserInterface|null $user
     * @return SettingsManager
     */
    private function setWithoutFlush($name, $value, UserInterface $user = null)
    {
        $this->validateSetting($name, $user);
        $this->loadSettings($user);

        if ($user === null) {
            $this->globalSettings[$name] = $value;
        } else {
            $this->userSettings[$user->getUsername()][$name] = $value;
        }

        return $this;
    }

    /**
     * Flushes settings defined by $names to database
     *
     * @param string|array $names
     * @param UserInterface|null $user
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return SettingsManager
     */
    private function flush($names, UserInterface $user = null)
    {
        $names = (array)$names;

        $settings = $this->repository->findBy(array('name' => $names, 'username' => $user === null ? null : $user->getUsername()));
        $findByName = function ($name) use ($settings) {
            $setting = array_filter(
                $settings,
                function (Setting $setting) use ($name) {
                    return $setting->getName() == $name;
                }
            );

            return !empty($setting) ? array_pop($setting) : null;
        };

        /** @var Setting $setting */
        foreach ($this->settingsConfiguration as $name => $configuration) {

            try {
                $value = $this->get($name, $user);
            } catch (WrongScopeException $e) {
                continue;
            }

            $setting = $findByName($name);

            if (!$setting) {
                $setting = new Setting();
                $setting->setName($name);
                if ($user !== null) {
                    $setting->setUsername($user->getUsername());
                }
                $this->em->persist($setting);
            }

            $setting->setValue($this->serializer->serialize($value));
        }

        $this->em->flush();

        return $this;
    }

    /**
     * Checks that $name is valid setting and it's scope is also valid
     *
     * @param string $name
     * @param UserInterface $user
     * @return SettingsManager
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    private function validateSetting($name, UserInterface $user = null)
    {
        // Name validation
        if (!is_string($name) || !array_key_exists($name, $this->settingsConfiguration)) {
            throw new UnknownSettingException($name);
        }

        // Scope validation
        $scope = $this->settingsConfiguration[$name]['scope'];
        if ($scope !== SettingsManagerInterface::SCOPE_ALL) {
            if ($scope === SettingsManagerInterface::SCOPE_GLOBAL && $user !== null || $scope === SettingsManagerInterface::SCOPE_USER && $user === null) {
                throw new WrongScopeException($scope, $name);
            }
        }

        return $this;
    }

    /**
     * Settings lazy loading
     *
     * @param UserInterface|null $user
     * @return SettingsManager
     */
    private function loadSettings(UserInterface $user = null)
    {
        // Global settings
        if ($this->globalSettings === null) {
            $this->globalSettings = $this->getSettingsFromRepository();
        }

        // User settings
        if ($user !== null && ($this->userSettings === null || !array_key_exists($user->getUsername(), $this->userSettings))) {
            $this->userSettings[$user->getUsername()] = $this->getSettingsFromRepository($user);
        }

        return $this;
    }

    /**
     * Retreives settings from repository
     *
     * @param UserInterface|null $user
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return array
     */
    private function getSettingsFromRepository(UserInterface $user = null)
    {
        $settings = array();

        foreach (array_keys($this->settingsConfiguration) as $name) {
            try {
                $this->validateSetting($name, $user);
                $settings[$name] = null;
            } catch (WrongScopeException $e) {
                continue;
            }
        }

        /** @var Setting $setting */
        foreach ($this->repository->findBy(array('username' => $user === null ? null : $user->getUsername())) as $setting) {
            if (array_key_exists($setting->getName(), $settings)) {
                $settings[$setting->getName()] = $this->serializer->unserialize($setting->getValue());
            }
        }

        return $settings;
    }
}
