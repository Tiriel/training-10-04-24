<?php

namespace App\Movie\Search\Transformer;

interface TransformerInterface
{
    public function transform(array|string $value): object;
}
