<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findAllByUser(User $user): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(['t.id', 't.type', 't.date', 't.amount', 'c.code'])
            ->where('t.billing_user = :user')
            ->setParameter('user', $user->getId())
            ->leftJoin('t.course', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 't.course = c.id')
            ->getQuery()
        ;

        return $query
            ->getResult()
        ;
    }

    public function findUserCourses(User $user): array
    {
        $qb = $this->createQueryBuilder('t');
        return $qb
            ->select(['t.id', 'c.type', 't.date', 'c.code', 't.expiration'])
            ->where(
                $qb->expr()->andX(
                    't.billing_user = :user',
                    't.type = 1',
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            't.expiration >= :today',
                            'c.type = :course_rent'
                        ),
                        'c.type != :course_rent'
                    )
                )
            )
            ->setParameter('user', $user->getId())
            ->setParameter('today', new \DateTime())
            ->setParameter('course_rent', COURSE_TYPE_RENT)
            ->leftJoin('t.course', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 't.course = c.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllPreExpiredCourses(): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(['u.email', 'c.name', 't.expiration'])
            ->where('t.type = 1') // transaction type is TRANSACTION_PAYMENT
            ->andWhere("t.expiration < :tomorrow")
            ->andWhere("t.expiration > :today")
            ->leftJoin('t.course', 'c', \Doctrine\ORM\Query\Expr\Join::WITH, 't.course = c.id')
            ->leftJoin('t.billing_user', 'u', \Doctrine\ORM\Query\Expr\Join::WITH, 't.billing_user = u.id')
            ->setParameter('tomorrow', (new \DateTime('now'))->modify('+1 day'))
            ->setParameter('today', new \DateTime())
            ->orderBy('u.email', 'ASC')
            ->getQuery();

        return $query
            ->getResult()
        ;
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
