<?php

namespace Dmishh\SettingsBundle\Exception;

use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;

class WrongScopeException extends \LogicException implements SettingsException
{
    public function __construct(string $scope, string $settingName)
    {
        if (SettingsManagerInterface::SCOPE_GLOBAL === $scope) {
            $message = sprintf(
                'You tried to access setting "%s" but it is in the "%s" scope which means you must not use a SettingOwnerInterface object with this option.',
                $settingName,
                $scope
            );
        } elseif (SettingsManagerInterface::SCOPE_USER === $scope) {
            $message = sprintf(
                'You tried to access setting "%s" but it is in the "%s" scope which means you have to pass a SettingOwnerInterface object with this option.',
                $settingName,
                $scope
            );
        } else {
            $message = sprintf('Wrong scope "%s" for setting "%s". Check your configuration.', $scope, $settingName);
        }

        parent::__construct($message);
    }
}
