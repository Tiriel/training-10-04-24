<?php

namespace App\Movie\Search\Provider;

use App\Entity\Movie;
use App\Movie\Search\Consumer\OmdbApiConsumer;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Transformer\MovieTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Validator\Constraints\When;

class TraceableCliMovieProvider extends MovieProvider
{
    protected ?SymfonyStyle $io = null;

    public function __construct(
        protected readonly MovieProvider $inner,
    )
    {
    }

    public function setIo(?SymfonyStyle $io): void
    {
        $this->io = $io;
    }

    protected function searchInDb(SearchType $type, string $value): ?Movie
    {
        $this->io?->text('Searching in database...');

        return $this->inner->searchInDb($type, $value);
    }

    protected function returnEntity(Movie $movie): Movie
    {
        $this->io?->note('Movie already in database!');

        return $this->inner->returnEntity($movie);
    }

    protected function searchOmdb(SearchType $type, string $value): array
    {
        $this->io?->text('Not found. Searching on OMDb API.');

        return $this->inner->searchOmdb($type, $value);
    }

    protected function buildMovie(array $data): Movie
    {
        $this->io?->note('Found on OMDb!');

        return $this->inner->buildMovie($data);
    }

    protected function saveMovie(Movie $movie): void
    {
        $this->io?->text('Saving movie in database...');
        $this->inner->saveMovie($movie);
    }
}
