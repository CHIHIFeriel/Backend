<?php

namespace App\Repository;

use App\Entity\Emplois;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emplois>
 *
 * @method Emplois|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emplois|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emplois[]    findAll()
 * @method Emplois[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emplois::class);
    }

    public function save(Emplois $emplois, $flush = true)
    {
        $em = $this->getEntityManager();
        $em->persist($emplois);

        if ($flush) {
            $em->flush();
        }
    }
}
