<?php

namespace Redking\Bundle\OAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RedkingOAuthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['facebook'])) {
            $container->setParameter('redking_oauth.facebook.id', $config['facebook']['id']);
            $container->setParameter('redking_oauth.facebook.secret', $config['facebook']['secret']);
        } else {
            $container->setParameter('redking_oauth.facebook.id', null);
            $container->setParameter('redking_oauth.facebook.secret', null);
        }
        $container->setParameter('redking_oauth.user_ws_role', $config['user_ws_role']);
        

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
