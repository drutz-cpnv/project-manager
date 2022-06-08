<?php

namespace App\Repository;

use App\Entity\Mandate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mandate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mandate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mandate[]    findAll()
 * @method Mandate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mandate::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Mandate $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Mandate $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Mandate[]
     */
    public function findForDireJe(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.state > 1')
            ->andWhere('m.state != 4')
            ->orderBy('m.uid', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Mandate[]
     */
    public function findDireJePending(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.state == 2')
            ->orderBy('m.uid', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Mandate[]
     */
    public function findDireJeDispatched(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.state = 4')
            ->orderBy('m.uid', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function reset()
    {
        $tableName = $this->getClassMetadata()->getTableName();
        $connection = $this->getEntityManager()->getConnection();
        $connection->exec("ALTER TABLE `". $tableName ."` AUTO_INCREMENT = 1");
    }

    public function getTableName()
    {
        
    }


    // /**
    //  * @return Mandate[] Returns an array of Mandate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mandate
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
