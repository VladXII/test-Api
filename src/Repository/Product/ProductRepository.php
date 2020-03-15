<?php

namespace App\Repository\Product;

use App\Entity\Brand\Brand;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function findAllProducts()
    {
        $qb = $this->createQueryBuilder('product')
            ->select('product')
            ->join(Brand::class, 'brand', 'WITH');

        return $qb->getQuery();
    }

    public function findAllProductsByBrand($brand)
    {
        $qb = $this->createQueryBuilder('product')
            ->select('product')
            ->join(Brand::class, 'brand', 'WITH')
            ->where('product.brand = :brand')
            ->setParameter('student', $brand);

        return $qb->getQuery();
    }
}
