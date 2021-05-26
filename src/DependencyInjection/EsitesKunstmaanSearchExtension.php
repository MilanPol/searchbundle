<?php

namespace Esites\KunstmaanSearchBundle\DependencyInjection;

use Esites\KunstmaanSearchBundle\Constant\ConfigConstants;
use Esites\KunstmaanSearchBundle\Search\NodeSearcher;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

class EsitesKunstmaanSearchExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $configuration = new Configuration();
        $config = $this->processConfiguration(
            $configuration,
            $configs
        );

        foreach (ConfigConstants::getConfiguration() as $configuration) {
            $container->setParameter(
                ConfigConstants::getParameterKeyName(
                    $configuration
                ),
                $config[$configuration]
            );
        }

        $container->setParameter(
            'kunstmaan_node_search.search.node.class',
            NodeSearcher::class
        );

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }

    public function getAlias(): string
    {
        return ConfigConstants::PREFIX_KEY;
    }
}
