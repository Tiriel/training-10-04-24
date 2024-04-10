<?php

namespace App\Movie\Search;

use App\Entity\Movie;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Provider\MovieProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTargetedValueResolver('movie')]
class MovieValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly MovieProvider $provider
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        $value = $request->attributes->get('title');

        if (
            !$type
            || !$type === Movie::class
            || !\is_string($value)
        ) {
            return [];
        }

        return [$this->provider->getOne(SearchType::Title, $value)];
    }
}
