<?php

namespace Worm\SiteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('worm_site');

        $rootNode
            ->children()
                ->arrayNode('queue')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_path')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('delay')
                            ->isRequired()
                            ->defaultValue('+1 Week')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}