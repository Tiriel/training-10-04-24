<?php

namespace App\Movie\Search\Consumer;

use App\Movie\Search\Enum\SearchType;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[When('prod')]
#[AsDecorator(OmdbApiConsumer::class)]
class CacheableOmdbApiConsumer extends OmdbApiConsumer
{
    public function __construct(
        protected OmdbApiConsumer $inner,
        protected CacheInterface $cache,
        protected SluggerInterface $slugger,
    )
    {
    }

    public function fetch(SearchType $type, string $value): array
    {
        $key = $this->slugger->slug(sprintf("%s_%s", $type->getApiParam(), $value));

        return $this->cache->get(
            $key,
            function (CacheItem $item) use ($type, $value) {
                $item->expiresAfter(3600);

                return $this->inner->fetch($type, $value);
            }
        );
    }
}
