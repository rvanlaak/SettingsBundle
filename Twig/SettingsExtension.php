<?php

namespace Dmishh\SettingsBundle\Twig;

use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension for retrieving settings in Twig templates.
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 */
class SettingsExtension extends AbstractExtension
{
    private $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_setting', [$this->settingsManager, 'get']),
            new TwigFunction('get_all_settings', [$this->settingsManager, 'all']),
        ];
    }
}
