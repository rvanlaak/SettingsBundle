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

use Symfony\Component\Security\Core\User\UserInterface;

interface SettingsManagerInterface
{
    const SCOPE_ALL    = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER   = 'user';

    /**
     * Returns setting value by its name
     *
     * @param string $name
     * @param UserInterface|null $user
     * @return mixed
     */
    function get($name, UserInterface $user = null);

    /**
     * Returns all settings as associative name-value array
     *
     * @param UserInterface|null $user
     * @return array
     */
    function all(UserInterface $user = null);

    /**
     * Sets setting value by its name
     *
     * @param string $name
     * @param mixed $value
     * @param UserInterface|null $user
     * @return SettingsManagerInterface
     */
    function set($name, $value, UserInterface $user = null);

    /**
     * Sets settings' values from associative name-value array
     *
     * @param array $settings
     * @param UserInterface|null $user
     * @return SettingsManagerInterface
     */
    function setMany(array $settings, UserInterface $user = null);

    /**
     * Clears setting value
     *
     * @param string $name
     * @param UserInterface|null $user
     * @return SettingsManagerInterface
     */
    function clear($name, UserInterface $user = null);
}
