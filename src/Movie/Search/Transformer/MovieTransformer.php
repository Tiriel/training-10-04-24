<?php

namespace App\Movie\Search\Transformer;

use App\Entity\Movie;

class MovieTransformer implements TransformerInterface
{
    private const KEYS = [
        'Title',
        'Plot',
        'Country',
        'Released',
        'Year',
        'Poster',
        'Rated',
        'imdbID',
    ];
    public function transform(array|string $value): Movie
    {
        if (
            !\is_array($value)
            || 0 < \count(\array_diff(self::KEYS, array_keys($value)))
        ) {
            throw new \InvalidArgumentException();
        }

        $date = $value['Released'] === 'N/A' ? '01-01-'.$value['Year'] : $value['Released'];

        return (new Movie())
            ->setTitle($value['Title'])
            ->setPlot($value['Plot'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setPoster($value['Poster'])
            ->setRated($value['Rated'])
            ->setImdbId($value['imdbID'])
        ;
    }
}
