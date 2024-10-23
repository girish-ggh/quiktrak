<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    // Custom save method
    public function save(Job $job, bool $flush = true): void
    {
        $this->getEntityManager()->persist($job);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Optional: Custom method to find jobs by status
    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    // Optional: Custom method to find jobs by inspector
    public function findByInspector($inspector)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.inspector = :inspector')
            ->setParameter('inspector', $inspector)
            ->getQuery()
            ->getResult();
    }

    // Optional: Custom method to find all jobs ordered by scheduled date
    public function findAllOrderedByScheduledDate()
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.scheduledDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

