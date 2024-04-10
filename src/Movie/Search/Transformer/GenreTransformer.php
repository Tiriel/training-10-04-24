<?php

namespace App\Movie\Search\Transformer;

use App\Entity\Genre;

class GenreTransformer implements TransformerInterface
{
    public function transform(array|string $value): Genre
    {
        if (!\is_string($value) || str_contains($value, ', ')) {
            throw new \InvalidArgumentException();
        }

        return (new Genre())->setName($value);
    }
}
