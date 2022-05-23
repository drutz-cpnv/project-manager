<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private UserRepository $userRepository,
    )
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Person $entity, bool $flush = true): void
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
    public function remove(Person $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOneByEmail($value): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllCoach()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.canCoach = 1')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()

            ;
    }

    /**
     * @return Person[]
     */
    public function findNonCoach()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.canCoach = 0')
            ->andWhere('p.type = 3')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Person[]
     */
    public function findAllStudents()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = 2')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllTeachers()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = 3')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllClients()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = 1')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Person[]
     */
    public function findAllNonUser()
    {
        /** @var Person[] $persons */
        $persons = $this->createQueryBuilder('p')
            ->orderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        $output = [];

        foreach ($persons as $person) {
            if (is_null($person->getUser())) $output[] = $person;
        }
        return $output;
    }

    // /**
    //  * @return Person[] Returns an array of Person objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findAllCopil()
    {
        return $this->userRepository->findByRole(3);
    }
}
