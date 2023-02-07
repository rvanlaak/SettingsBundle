<?php

namespace Dmishh\SettingsBundle\Exception;

class UnknownSettingException extends \RuntimeException implements SettingsException
{
    public function __construct(string $settingName)
    {
        parent::__construct(sprintf('Unknown setting "%s"', $settingName));
    }
}
