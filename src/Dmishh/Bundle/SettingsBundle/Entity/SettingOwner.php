<?php

namespace Dmishh\Bundle\SettingsBundle\Entity;

/**
 * This interface must be implemented by the Entity connected to a setting
 */
interface SettingOwner
{
    public function getSettingIdentifier();
} 