<?php

namespace App\Book;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookManager
{
    public function __construct(
        protected EntityManagerInterface $manager,
        protected int $limit,
    )
    {
    }

    public function findOne(int $id): ?Book
    {
        return $this->manager->find(Book::class, $id);
    }

    public function findPaginated(int $page): iterable
    {
        return $this->manager
            ->getRepository(Book::class)
            ->findBy([], [], $this->limit, ($page - 1) * $this->limit);
    }
}
