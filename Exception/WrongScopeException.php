<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\SettingsBundle\Exception;

use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;

class WrongScopeException extends \LogicException implements SettingsException
{
    public function __construct($scope, $settingName)
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
