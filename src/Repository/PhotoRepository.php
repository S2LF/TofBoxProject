<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    // /**
    //  * @return Photo[] Returns an array of Photo objects
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
    public function findOneBySomeField($value): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAll(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager ->createQuery(
                    "SELECT p
                        FROM App\Entity\Photo p"
        );
        return $query->execute();
    }

    public function getAllPages(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager ->createQuery(
                    "SELECT p
                        FROM App\Entity\Photo p
                        ORDER BY p.date_creation DESC"
        );
        return $query;
    }

    public function getPopularPages()
    {
        $qb = $this->createQueryBuilder('p')
        ->select('COUNT(u) AS HIDDEN nb', 'p')
        ->leftJoin('p.likeUsers', 'u')
        ->orderBy('nb', 'DESC')
        ->groupBy('p');
        return $qb->getQuery();
    }

    public function getPhotosFromUser($userId){

        return $this->createQueryBuilder('p')
        ->where( 'p.user = '.$userId)
        ->orderBy('p.date_creation', 'DESC')
        ->getQuery()
        ;
     }

     public function findLasts($number)
     {
         return $this->createQueryBuilder('p')
             ->orderBy('p.date_creation', 'DESC')
             ->setMaxResults($number)
             ->getQuery()
             ->getResult()
         ;
     }
     public function findLastsByPop($number)
     {
         return $this->createQueryBuilder('p')
         ->select('COUNT(u) AS HIDDEN nb', 'p')
         ->leftJoin('p.likeUsers', 'u')
         ->orderBy('nb', 'DESC')
         ->groupBy('p')
         ->setMaxResults($number)
         ->getQuery()
         ->getResult();
     }

    public function findLastsByUser($number, $user)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.date_creation', 'DESC')
            ->where( 'p.user = '.$user)
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findByFollowedRandom($number, $user)
    // {
    //     return $this->createQueryBuilder('p')
    //         ->from('User', 'u')
    //         ->where('User = '.$user)
    // }

    // public function findByFollowedRandom($number, $user){
    //     $entityManager = $this->getEntityManager();
    //     $query = $entityManager ->createQuery(
    //                 "SELECT p
    //                     FROM App\Entity\Photo p, App\Entity\User u
    //                     WHERE u.id = $user
    //                     AND p.user = u.followedUsers"
                        
    //     );
    //     return $query->execute();
    // }

    public function findByFollowedRandom($users){
        $entityManager = $this->getEntityManager();
        $query = $entityManager ->createQuery(
                    "SELECT p
                        FROM App\Entity\Photo p
                        WHERE p.user = $users"
        );
        return $query->execute();
    }


    // public function findByFollowedRandom($userId){
    //     $entityManager = $this->getEntityManager();
    //     $query = $entityManager ->createQuery(
    //                 "SELECT p
    //                     FROM App\Entity\Photo p
    //                     WHERE p.user IN
    //                     (SELECT u FROM App\Entity\User u JOIN u.followedUsers f WHERE u = $userId)"
    //     );
    //     return $query->execute();
    // }

    public function deletePhoto($userId)
    {
        return $this->createQueryBuilder('p')
            ->delete()
            ->andWhere('p.user = '. $userId)
            ->getQuery()
            ->getResult();
    }

}
