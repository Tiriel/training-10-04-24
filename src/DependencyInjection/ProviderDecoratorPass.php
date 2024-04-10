<?php

namespace App\DependencyInjection;

use App\Movie\Search\Provider\MovieProvider;
use App\Movie\Search\Provider\TraceableCliMovieProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProviderDecoratorPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if ('cli' === \PHP_SAPI) {
            $container->register(TraceableCliMovieProvider::class, TraceableCliMovieProvider::class)
                ->setDecoratedService(MovieProvider::class)
                ->setAutowired(true)
                ->setAutoconfigured(true)
            ;
        }
    }
}
