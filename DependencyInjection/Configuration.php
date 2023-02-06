<?php

namespace Dmishh\SettingsBundle\DependencyInjection;

use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dmishh_settings');
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root('dmishh_settings');
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }

        $scopes = [
            SettingsManagerInterface::SCOPE_ALL,
            SettingsManagerInterface::SCOPE_GLOBAL,
            SettingsManagerInterface::SCOPE_USER,
        ];

        $rootNode
            ->children()
                ->scalarNode('template')
                    ->defaultValue('@DmishhSettings/Settings/manage.html.twig')
                ->end()
                ->scalarNode('cache_service')->defaultNull()->info('A service implementing Psr\Cache\CacheItemPoolInterface')->end()
                ->integerNode('cache_lifetime')->defaultValue(3600)->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('manage_global_settings_role')->defaultValue(null)->end()
                        ->booleanNode('users_can_manage_own_settings')->defaultValue(true)->end()
                    ->end()
                ->end()
                ->enumNode('serialization')
                    ->defaultValue('php')
                    ->values(['php', 'json'])
                ->end()
                ->arrayNode('settings')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('scope')
                                ->defaultValue('all')
                                ->validate()
                                    ->ifNotInArray($scopes)
                                    ->thenInvalid('Invalid scope %s. Valid scopes are: '.implode(', ', array_map(function ($s) { return '"'.$s.'"'; }, $scopes)).'.')
                                ->end()
                            ->end()
                            ->scalarNode('type')->defaultValue(TextType::class)->end()

                            ->variableNode('options')
                                ->info('The options given to the form builder')
                                ->defaultValue([])
                                ->validate()
                                    ->always(function ($v) {
                                        if (!\is_array($v)) {
                                            throw new InvalidTypeException();
                                        }

                                        return $v;
                                    })
                                ->end()
                            ->end()
                            ->variableNode('constraints')
                                ->info('The constraints on this option. Example, use constraits found in Symfony\Component\Validator\Constraints')
                                ->defaultValue([])
                                ->validate()
                                    ->always(function ($v) {
                                        if (!\is_array($v)) {
                                            throw new InvalidTypeException();
                                        }

                                        return $v;
                                    })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
