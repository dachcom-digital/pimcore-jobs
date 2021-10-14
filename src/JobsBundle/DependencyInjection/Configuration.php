<?php

namespace JobsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jobs');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('context')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('host')->defaultNull()->end()
                            ->scalarNode('locale')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('data_class')->defaultValue(null)->cannotBeEmpty()->end()
                ->scalarNode('feed_host')->defaultValue(null)->end()
                ->integerNode('log_expiration_days')->defaultValue(30)->end()
                ->arrayNode('available_connectors')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('connector_name')->cannotBeEmpty()->isRequired()->end()
                            ->scalarNode('connector_item_transformer')->cannotBeEmpty()->isRequired()->end()
                            ->variableNode('connector_config')->defaultValue([])->end()
                            ->arrayNode('connector_items_resolver')
                                ->prototype('array')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('type')->cannotBeEmpty()->isRequired()->end()
                                        ->variableNode('config')->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $rootNode->append($this->createPersistenceNode());

        return $treeBuilder;
    }

    private function createPersistenceNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('persistence');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->performNoDeepMerging()
            ->children()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity_manager')
                            ->info('Name of the entity manager that you wish to use for managing form builder entities.')
                            ->cannotBeEmpty()
                            ->defaultValue('default')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
