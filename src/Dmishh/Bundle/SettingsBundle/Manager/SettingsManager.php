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
use Dmishh\Bundle\SettingsBundle\Entity\UserInterface;
use Dmishh\Bundle\SettingsBundle\Exception\UnknownSettingException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var array
     */
    protected $globalSettings = [];

    /**
     * @var array
     */
    protected $userSettings = [];

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $options = array(
        'setting_names' => [],
        'inherit_global_settings' => true
    );

    /**
     * @param ObjectManager $em
     * @param array $options
     */
    public function __construct(ObjectManager $em, array $options = array())
    {
        $this->em = $em;
        $this->repository = $em->getRepository('Dmishh\\Bundle\\SettingsBundle\\Entity\\Setting');

        foreach ($options as $name => $value) {
            if (array_key_exists($name, $this->options)) {
                $this->options[$name] = $value;
            }
        }
    }

    /**
     * @param $name
     * @param UserInterface $user
     * @return mixed
     */
    public function get($name, UserInterface $user = null)
    {
        $this->validateSettingName($name);
        $this->loadSettings($user);

        $value = null;

        if ($user === null) {
            $value = $this->globalSettings[$name];
        } else {
            if ($this->userSettings[$user->getId()][$name] !== null) {
                $value = $this->userSettings[$user->getId()][$name];
            } elseif ($this->options['inherit_global_settings']) {
                $value = $this->globalSettings[$name];
            }
        }

        return $value;
    }

    public function set($name, $value, UserInterface $user = null)
    {
        $this->setWithoutFlush($name, $value, $user);
        return $this->flush($name, $user);
    }

    public function setMany($settings, UserInterface $user = null)
    {
        foreach ($settings as $name => $value) {
            $this->setWithoutFlush($name, $value, $user);
        }

        return $this->flush(array_keys($settings), $user);
    }

    protected function setWithoutFlush($name, $value, UserInterface $user = null)
    {
        $this->validateSettingName($name);
        $this->loadSettings($user);

        if ($user === null) {
            $this->globalSettings[$name] = $value;
        } else {
            $this->userSettings[$user->getId()][$name] = $value;
        }

        return $this;
    }

    /**
     * @param $name
     * @param UserInterface $user
     * @return mixed
     */
    public function clear($name, UserInterface $user = null)
    {
        $this->validateSettingName($name);
        $this->loadSettings($user);

        if ($user === null) {
            $this->globalSettings[$name] = null;
        } else {
            $this->userSettings[$user->getId()][$name] = null;
        }

        return $this->flush($name, $user);
    }

    protected function flush($names, UserInterface $user = null)
    {
        $names = (array) $names;

        $settings = $this->repository->findBy(array('name' => $names, 'userId' => $user === null ? null : $user->getId()));
        $findByName = function ($name) use ($settings) {
            $setting = array_filter($settings, function ($setting) use ($name) {
                    return $setting->getName() == $name;
                });
            return !empty($setting) ? $setting[0] : null;
        };
        /** @var Setting $setting */
        foreach ($this->options['setting_names'] as $name) {
            $setting = $findByName($name);
            if (!$setting) {
                $setting = new Setting();
                $setting->setName($name);
                $this->em->persist($setting);
            }

            if ($user === null) {
                $setting->setValue($this->globalSettings[$setting->getName()]);
            } else {
                $setting->setUserId($user->getId());
                $setting->setValue($this->userSettings[$user->getId()][$setting->getName()]);
            }
        }

        $this->em->flush();

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function all(UserInterface $user = null)
    {
        $this->loadSettings($user);

        if ($user === null) {
            return $this->globalSettings;
        } else {
            return $this->userSettings[$user->getId()];
        }
    }

    protected function validateSettingName($name)
    {
        if (!is_string($name) || !in_array($name, $this->options['setting_names'])) {
            throw new UnknownSettingException($name);
        }

        return $this;
    }

    protected function loadSettings(UserInterface $user = null)
    {
        $defaultSettings = array_fill_keys($this->options['setting_names'], null);

        // filling global settings with defaults
        if (empty($this->globalSettings)) {
            $this->globalSettings = $defaultSettings;

            $settings = $this->repository->findBy(array('userId' => null));
            /** @var Setting $setting */
            foreach ($settings as $setting) {
                if (array_key_exists($setting->getName(), $this->globalSettings)) {
                    $this->globalSettings[$setting->getName()] = $setting->getValue();
                }
            }
        }

        if ($user !== null) {
            // filling user settings with defaults
            if (empty($this->userSettings[$user->getId()])) {
                $this->userSettings[$user->getId()] = $defaultSettings;

                $settings = $this->repository->findBy(array('userId' => $user->getId()));
                /** @var Setting $setting */
                foreach ($settings as $setting) {
                    if (array_key_exists($setting->getName(), $this->userSettings[$user->getId()])) {
                        $this->userSettings[$user->getId()][$setting->getName()] = $setting->getValue();
                    }
                }
            }
        }

        return $this;
    }
}
