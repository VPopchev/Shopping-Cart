<?php

namespace AppBundle\Repository;

/**
 * ProductCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductCategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function getProductsWithCategory(){
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT a,c FROM AppBundle:ProductCategory a
                              JOIN a.products c');
        return $query->getResult();
    }
}
