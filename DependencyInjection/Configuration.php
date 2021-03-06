<?php

namespace Redking\Bundle\OAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('redking_o_auth');

        // Define facebook configuration data
        $rootNode
            ->children()
                ->arrayNode('facebook')
                    ->children()
                        ->scalarNode('id')->isRequired()->end()
                        ->scalarNode('secret')->isRequired()->end()
                    ->end()
                ->end()
                ->scalarNode('user_ws_role')->defaultValue('ROLE_WS_USER')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
