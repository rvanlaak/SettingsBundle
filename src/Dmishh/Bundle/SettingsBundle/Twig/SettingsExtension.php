<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Twig;

use Dmishh\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface;

/**
 * Extension for retrieving settings in Twig templates
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 */
class SettingsExtension extends \Twig_Extension
{
    /**
     * @var \Dmishh\Bundle\SettingsBundle\Manager\SettingsManagerInterface
     */
    private $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_setting', array($this, 'getSetting')),
            new \Twig_SimpleFunction('get_all_settings', array($this, 'getAllSettings')),
        );
    }

    /**
     * Proxy to SettingsManager::get
     *
     * @param string                      $name
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function getSetting($name, SettingsOwnerInterface $owner = null, $default = null)
    {
        return $this->settingsManager->get($name, $owner, $default);
    }

    /**
     * Proxy to SettingsManager::all
     *
     * @param SettingsOwnerInterface|null $owner
     *
     * @return array
     */
    public function getAllSettings(SettingsOwnerInterface $owner = null)
    {
        return $this->settingsManager->all($owner);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'settings_extension';
    }
}
