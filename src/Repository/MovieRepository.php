<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Movie\Enum\SearchType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function getQueryBuilderForPagination(): QueryBuilder
    {
        return $this->createQueryBuilder('m');
    }

    public function findLikeOmdb(SearchType $type, string $value): ?Movie
    {
        $qb = $this->getWhereClauseForType($type, $value);

        return $qb->orderBy('m.releasedAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getWhereClauseForType(SearchType $type, string $value): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m');

        if (SearchType::Title === $type) {
            $qb->andWhere($qb->expr()->like('m.title', ':value'))
                ->setParameter('value', "%$value%");

            return $qb;
        }

        $qb->andWhere($qb->expr()->eq('m.imdbId', ':value'))
            ->setParameter('value', $value);

        return $qb;
    }

    //    /**
    //     * @return Movie[] Returns an array of Movie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Movie
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
