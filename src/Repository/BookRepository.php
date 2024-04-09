<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book, bool $flush = false): void
    {
        $this->getEntityManager()->persist($book);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $book, bool $flush = false): void
    {
        $this->getEntityManager()->remove($book);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByLikeTitle(string $title): Book
    {
        $qb = $this->createQueryBuilder('b');
        $subQb = $this->createQueryBuilder('b1');

        return $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->like('b.title', ':title'),
                    $qb->expr()->isNotNull('b.cover')
                )
            )
            ->setParameter('title', '%'.$title.'%')
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
    }
/*    private function addTechnologyToQB(QueryBuilder $qb, string $technology): void
    {
        // where technology.name = $technology OR technology.parent.name = $technology
        $qb->innerJoin('q.technology', 't')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('t.name', ':technology'),
                $qb->expr()->eq('t.parent', '('.$this->getEntityManager()->createQueryBuilder()
                        ->select('t2.id')
                        ->from(Technology::class, 't2')
                        ->where('t2.name = :technology')
                        ->setParameter('technology', $technology)
                        ->getDQL().')'
                )
            ))
            ->setParameter('technology', $technology);
    }*/

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
