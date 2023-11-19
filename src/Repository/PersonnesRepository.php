<?php

namespace App\Repository;

use App\Entity\Personnes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personnes>
 *
 * @method Personnes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnes[]    findAll()
 * @method Personnes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnes::class);
    }
    public function save(Personnes $person, $flush = true)
    {
        $em = $this->getEntityManager();
        $em->persist($person);

        if ($flush) {
            $em->flush();
        }
    }

    public function findPersonnesSansEmploi($dateDebut, $dateFin)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->leftJoin('p.emplois', 'e')
            ->andWhere('e.id IS NULL OR (e.dateDebut > :dateFin OR e.dateFin < :dateDebut)')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findPersonnesAvecEmploi(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.emplois', 'e')
            ->where('e.dateDebut BETWEEN :dateDebut AND :dateFin')
            ->orWhere('e.dateFin BETWEEN :dateDebut AND :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}
