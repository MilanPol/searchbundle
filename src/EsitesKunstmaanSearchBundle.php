<?php

namespace Esites\KunstmaanSearchBundle;

use Esites\KunstmaanSearchBundle\DependencyInjection\EsitesKunstmaanSearchExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EsitesKunstmaanSearchBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerExtension(new EsitesKunstmaanSearchExtension());
    }
}
