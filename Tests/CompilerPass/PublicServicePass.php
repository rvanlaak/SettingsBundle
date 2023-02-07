<?php

namespace Dmishh\SettingsBundle\Tests\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class PublicServicePass implements CompilerPassInterface
{
    public function __construct(private string $regex = '|.*|')
    {
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (preg_match($this->regex, $id)) {
                $definition->setPublic(true);
            }
        }

        foreach ($container->getAliases() as $id => $alias) {
            if (preg_match($this->regex, $id)) {
                $alias->setPublic(true);
            }
        }
    }
}
