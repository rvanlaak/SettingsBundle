<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DmishhSettingsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $globalSettings = ['template', 'security', 'layout'];
        foreach ($globalSettings as $key) {
            $container->setParameter('settings_manager.'.$key, $config[$key]);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Configure the correct storage
        if ($config['cache_service'] !== null) {
            $storage = new Reference($config['cache_service']);
            $cachedManager = $container->getDefinition('dmishh.settings.cached_settings_manager');
            $cachedManager->addMethodCall('setCacheStorage', array($storage));

            // set an alias to make sure the cached settings manager is the default
            $container->setAlias('settings_manager', 'dmishh.settings.cached_settings_manager');
        }

        $container->setParameter('dmishh.settings.cache.lifetime', $config['cache_lifetime']);
        $container->findDefinition('dmishh.settings.settings_manager')->replaceArgument(2, $config['settings']);
        $container->findDefinition('form.type.settings_management')->replaceArgument(0, $config['settings']);
        $container->findDefinition('dmishh.settings.serializer')->replaceArgument(0, $config['serialization']);
    }
}
