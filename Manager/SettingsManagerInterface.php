<?php

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    public const SCOPE_ALL = 'all';
    public const SCOPE_GLOBAL = 'global';
    public const SCOPE_USER = 'user';

    /**
     * Returns setting value by its name.
     *
     * @param mixed|null $default value to return if the setting is not set
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, mixed $default = null): mixed;

    /**
     * Returns all settings as associative name-value array.
     *
     * @return array<string, mixed>
     */
    public function all(?SettingsOwnerInterface $owner = null): array;

    /**
     * Sets setting value by its name.
     */
    public function set(string $name, mixed $value, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Sets settings' values from associative name-value array.
     *
     * @param array<string, mixed> $settings
     */
    public function setMany(array $settings, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Clears setting value.
     */
    public function clear(string $name, SettingsOwnerInterface $owner = null): void;
}
