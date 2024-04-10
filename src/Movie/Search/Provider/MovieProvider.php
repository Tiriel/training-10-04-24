<?php

namespace App\Movie\Search\Provider;

use App\Entity\Movie;
use App\Movie\Search\Enum\SearchType;

class MovieProvider
{
    public function getOne(SearchType $type, string $value): Movie
    {
        // search in DB
        // if found, return
        // if not:
        // make the call to OMDb
        // transform data
        // call GenreProvider
        // save movie
        // return movie
    }
}
