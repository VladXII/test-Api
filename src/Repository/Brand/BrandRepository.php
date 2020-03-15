<?php

namespace App\Repository\Brand;

use Doctrine\ORM\EntityRepository;

class BrandRepository extends EntityRepository
{
    public function findAllBrands()
    {
        $qb = $this->createQueryBuilder('brand')
            ->select('brand');

        return $qb->getQuery();
    }
}
