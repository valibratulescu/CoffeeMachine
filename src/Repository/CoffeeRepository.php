<?php

namespace App\Repository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;

class CoffeeRepository extends EntityRepository
{
    public function getAvailableProducts(): array
    {
        $qb = $this->createQueryBuilder("p");

        return $qb
            ->where($qb->expr()->gt("p.availability", ":availability"))
            ->setParameter("availability", 0, Types::INTEGER)
            ->getQuery()
            ->getResult();
    }
}