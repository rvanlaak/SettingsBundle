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
use Dmishh\Bundle\SettingsBundle\Entity\SettingOwner;
use Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException;
use Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException;
use Dmishh\Bundle\SettingsBundle\Serializer\SerializerInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
    private $ownerSettings;

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
    public function get($name, SettingOwner $owner = null, $default = null)
    {
        $this->validateSetting($name, $owner);
        $this->loadSettings($owner);

        $value = null;

        switch ($this->settingsConfiguration[$name]['scope']) {
            case SettingsManagerInterface::SCOPE_GLOBAL:
                $value = $this->globalSettings[$name];
                break;
            case SettingsManagerInterface::SCOPE_ALL:
                $value = $this->globalSettings[$name];
                //Do not break here. Try to fetch the users settings
            case SettingsManagerInterface::SCOPE_USER:
                if ($owner !== null) {
                    if ($this->ownerSettings[$owner->getSettingIdentifier()][$name] !== null) {
                        $value = $this->ownerSettings[$owner->getSettingIdentifier()][$name];
                    }
                }
                break;
        }

        return $value === null ? $default : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function all(SettingOwner $owner = null)
    {
        $this->loadSettings($owner);

        if ($owner === null) {
            return $this->globalSettings;
        } else {
            return $this->ownerSettings[$owner->getSettingIdentifier()];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($name, $value, SettingOwner $owner = null)
    {
        $this->setWithoutFlush($name, $value, $owner);

        return $this->flush($name, $owner);
    }

    /**
     * {@inheritDoc}
     */
    public function setMany(array $settings, SettingOwner $owner = null)
    {
        foreach ($settings as $name => $value) {
            $this->setWithoutFlush($name, $value, $owner);
        }

        return $this->flush(array_keys($settings), $owner);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($name, SettingOwner $owner = null)
    {
        return $this->set($name, null, $owner);
    }

    /**
     * Sets setting value to private array. Used for settings' batch saving
     *
     * @param string $name
     * @param mixed $value
     * @param SettingOwner|null $owner
     *
     * @return SettingsManager
     */
    private function setWithoutFlush($name, $value, SettingOwner $owner = null)
    {
        $this->validateSetting($name, $owner);
        $this->loadSettings($owner);

        if ($owner === null) {
            $this->globalSettings[$name] = $value;
        } else {
            $this->ownerSettings[$owner->getSettingIdentifier()][$name] = $value;
        }

        return $this;
    }

    /**
     * Flushes settings defined by $names to database
     *
     * @param string|array $names
     * @param SettingOwner|null $owner
     *
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return SettingsManager
     */
    private function flush($names, SettingOwner $owner = null)
    {
        $names = (array)$names;

        $settings = $this->repository->findBy(array('name' => $names, 'ownerId' => $owner === null ? null : $owner->getSettingIdentifier()));
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
                $value = $this->get($name, $owner);
            } catch (WrongScopeException $e) {
                continue;
            }

            $setting = $findByName($name);

            if (!$setting) {
                $setting = new Setting();
                $setting->setName($name);
                if ($owner !== null) {
                    $setting->setOwnerId($owner->getSettingIdentifier());
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
     * @param SettingOwner $owner
     *
     * @return SettingsManager
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException
     */
    private function validateSetting($name, SettingOwner $owner = null)
    {
        // Name validation
        if (!is_string($name) || !array_key_exists($name, $this->settingsConfiguration)) {
            throw new UnknownSettingException($name);
        }

        // Scope validation
        $scope = $this->settingsConfiguration[$name]['scope'];
        if ($scope !== SettingsManagerInterface::SCOPE_ALL) {
            if ($scope === SettingsManagerInterface::SCOPE_GLOBAL && $owner !== null || $scope === SettingsManagerInterface::SCOPE_USER && $owner === null) {
                throw new WrongScopeException($scope, $name);
            }
        }

        return $this;
    }

    /**
     * Settings lazy loading
     *
     * @param SettingOwner|null $owner
     *
     * @return SettingsManager
     */
    private function loadSettings(SettingOwner $owner = null)
    {
        // Global settings
        if ($this->globalSettings === null) {
            $this->globalSettings = $this->getSettingsFromRepository();
        }

        // User settings
        if ($owner !== null && ($this->ownerSettings === null || !array_key_exists(
                    $owner->getSettingIdentifier(), $this->ownerSettings))) {
            $this->ownerSettings[$owner->getSettingIdentifier()] = $this->getSettingsFromRepository($owner);
        }

        return $this;
    }

    /**
     * Retreives settings from repository
     *
     * @param SettingOwner|null $owner
     *
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return array
     */
    private function getSettingsFromRepository(SettingOwner $owner = null)
    {
        $settings = array();

        foreach (array_keys($this->settingsConfiguration) as $name) {
            try {
                $this->validateSetting($name, $owner);
                $settings[$name] = null;
            } catch (WrongScopeException $e) {
                continue;
            }
        }

        /** @var Setting $setting */
        foreach ($this->repository->findBy(array('ownerId' => $owner === null ? null : $owner->getSettingIdentifier())) as $setting) {
            if (array_key_exists($setting->getName(), $settings)) {
                $settings[$setting->getName()] = $this->serializer->unserialize($setting->getValue());
            }
        }

        return $settings;
    }
}
