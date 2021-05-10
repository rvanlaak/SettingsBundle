<?php

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    const SCOPE_ALL = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER = 'user';

    /**
     * Returns setting value by its name.
     *
     * @param string $name
     * @param SettingsOwnerInterface|null $owner
     * @param mixed|null $default value to return if the setting is not set
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     * @param SettingsOwnerInterface|null $owner
     * @return array
     */
    public function all(?SettingsOwnerInterface $owner = null): array;

    /**
     * Sets setting value by its name.
     * @param string $name
     * @param $value
     * @param SettingsOwnerInterface|null $owner
     */
    public function set(string $name, $value, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Sets settings' values from associative name-value array.
     * @param array $settings
     * @param SettingsOwnerInterface|null $owner
     */
    public function setMany(array $settings, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Clears setting value.
     * @param string $name
     * @param SettingsOwnerInterface|null $owner
     */
    public function clear(string $name, SettingsOwnerInterface $owner = null): void;
}
