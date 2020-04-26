<?php

namespace App\Repository;

use App\Entity\Follow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Follow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Follow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Follow[]    findAll()
 * @method Follow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follow::class);
    }

    // /**
    //  * @return Follow[] Returns an array of Follow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Follow
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAll(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager ->createQuery(
                    "SELECT f
                        FROM App\Entity\Follow f"
        );
        return $query->execute();
    }


    public function isFollow($userProfilId, $userCurrentId){

        $request = $this->createQueryBuilder('f')
                        ->andWhere('f.followedUsers ='. $userProfilId)
                        ->andWHere('f.followByUsers ='. $userCurrentId)
                        ->getQuery()
                        ->getResult();
        if($request){
            return true;
        } else {
            return false;
        }

    }

    public function deleteFollow($currentId, $userProfilId){
        $request = $this->createQueryBuilder('f')
        ->delete()
        ->andWhere('f.followByUsers ='. $currentId)
        ->andWhere('f.followedUsers ='. $userProfilId)
        ->getQuery()
        ->getResult();
    }


    public function deleteAllFollow($userId){
        $request = $this->createQueryBuilder('f')
            ->delete()
            ->andWhere('f.followedUsers ='. $userId)
            ->getQuery()
            ->getResult();

        $request = $this->createQueryBuilder('f')
            ->delete()
            ->andWhere('f.followByUsers ='. $userId)
            ->getQuery()
            ->getResult();
    }          
}
