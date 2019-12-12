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

class UnknownSettingException extends \RuntimeException implements SettingsException
{
    public function __construct($settingName)
    {
        parent::__construct(sprintf('Unknown setting "%s"', $settingName));
    }
}
