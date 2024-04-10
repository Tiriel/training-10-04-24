<?php

namespace App\Movie\Search\Provider;

use App\Entity\Movie;
use App\Movie\Search\Consumer\OmdbApiConsumer;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Transformer\MovieTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MovieProvider
{
    public function __construct(
        protected readonly EntityManagerInterface $manager,
        protected readonly OmdbApiConsumer $consumer,
        protected readonly MovieTransformer $transformer,
        protected readonly GenreProvider $genreProvider,
    )
    {
    }

    public function getOne(SearchType $type, string $value): ?Movie
    {
        $movie = $this->searchInDb($type, $value);

        if ($movie instanceof Movie) {
            return $this->returnEntity($movie);
        }

        try {
            $data = $this->searchOmdb($type, $value);
        } catch (NotFoundHttpException) {
            return null;
        }

        $movie = $this->buildMovie($data);
        $this->saveMovie($movie);

        return $movie;
    }

    protected function searchInDb(SearchType $type, string $value): ?Movie
    {
        return $this->manager->getRepository(Movie::class)->findLikeOmdb($type, $value);
    }

    protected function returnEntity(Movie $movie): Movie
    {
        return $movie;
    }

    protected function searchOmdb(SearchType $type, string $value): array
    {
        return $this->consumer->fetch($type, $value);
    }

    protected function buildMovie(array $data): Movie
    {
        $movie = $this->transformer->transform($data);

        $genres = $this->genreProvider->getFromOmdbString($data['Genre']);
        foreach ($genres as $genre) {
            $movie->addGenre($genre);
        }

        return $movie;
    }

    protected function saveMovie(Movie $movie): void
    {
        $this->manager->persist($movie);
        $this->manager->flush();
    }
}
