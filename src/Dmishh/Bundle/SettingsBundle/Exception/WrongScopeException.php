<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Exception;

use Dmishh\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

class WrongScopeException extends SettingsException
{
    public function __construct($scope, $settingName)
    {
        if ($scope === SettingsManagerInterface::SCOPE_GLOBAL) {
            $message = sprintf('You tried to access setting "%s" but it is in the "%s" scope which means you must not use a SettingOwnerInterface object with this option.', $settingName, $scope);
        } elseif ($scope === SettingsManagerInterface::SCOPE_USER) {
            $message = sprintf('You tried to access setting "%s" but it is in the "%s" scope which means you have to pass a SettingOwnerInterface object with this option.', $settingName, $scope);
        } else {
            $message = sprintf('Wrong scope "%s" for setting "%s". Check your configuration.', $scope, $settingName);
        }

        parent::__construct($message);
    }
}
