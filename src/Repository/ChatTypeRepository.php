<?php

namespace App\Repository;

use App\Entity\ChatType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatType>
 *
 * @method ChatType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatType[]    findAll()
 * @method ChatType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatType::class);
    }

    public function add(ChatType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChatType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchChatTypesAfter(string $MessageTime,int $limit=5):mixed
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.MessageTime > :val')
            ->setParameter('val', ($MessageTime))
            ->orderBy('p.MessageTime', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

    }
    public function fetchChatTypesBefore(string $MessageTime,int $limit=10):mixed
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.MessageTime < :val')
            ->setParameter('val', ($MessageTime))
            ->orderBy('p.MessageTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;

    }

    public function findeLastMessage()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.MessageTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;

    }

//    /**
//     * @return ChatType[] Returns an array of ChatType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChatType
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
