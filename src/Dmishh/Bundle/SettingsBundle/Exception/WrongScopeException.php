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

class WrongScopeException extends SettingsException
{
    public function __construct($scopeName, $settingName)
    {
        parent::__construct(sprintf('Wrong scope "%s" for setting "%s". Check your configuration and make sure that user is authenticated.', $scopeName, $settingName));
    }
}
