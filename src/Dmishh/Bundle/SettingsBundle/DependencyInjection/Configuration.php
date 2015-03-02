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

use Dmishh\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dmishh_settings');

        $scopes = array(
            SettingsManagerInterface::SCOPE_ALL,
            SettingsManagerInterface::SCOPE_GLOBAL,
            SettingsManagerInterface::SCOPE_USER,
        );

        $rootNode
            ->children()
                ->scalarNode('layout')
                    ->defaultValue('DmishhSettingsBundle::layout.html.twig')
                ->end()
                ->scalarNode('template')
                    ->defaultValue('DmishhSettingsBundle:Settings:manage.html.twig')
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('manage_global_settings_role')->end()
                        ->booleanNode('users_can_manage_own_settings')->defaultValue(true)->end()
                    ->end()
                ->end()
                ->enumNode('serialization')
                    ->defaultValue('php')
                    ->values(array('php', 'json'))
                ->end()
                ->arrayNode('settings')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('scope')
                                ->defaultValue('all')
                                ->validate()
                                    ->ifNotInArray($scopes)
                                    ->thenInvalid('Invalid scope %s. Valid scopes are: ' . implode(', ', array_map(function ($s) { return '"' . $s . '"'; }, $scopes)) . '.')
                                ->end()
                            ->end()
                            ->arrayNode('validation')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')
                                        ->defaultValue('text')
                                    ->end()
                                    ->variableNode('options')
                                        ->defaultValue(array())
                                        ->validate()
                                        ->always(function ($v) {
                                                if (!is_array($v)) {
                                                    throw new InvalidTypeException();
                                                }
                                                return $v;
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
