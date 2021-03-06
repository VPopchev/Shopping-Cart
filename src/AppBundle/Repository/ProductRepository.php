<?php

namespace AppBundle\Repository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em, new ClassMetadata(Product::class));
    }



    public function findAllActive(){
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT p,c FROM AppBundle:Product p
                              JOIN p.category c
                              WHERE p.isActive = 1');
        return $query->getResult();
    }

    public function getProductWithCategory(int $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT a,c FROM AppBundle:Product a
                              JOIN a.category c
                              WHERE a.id = ' . $id);
        return $query->getOneOrNullResult();
    }

    public function getProduct(int $id){
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT a,c,p FROM AppBundle:Product a
                              JOIN a.category c
                              JOIN a.promotions p
                              WHERE a.id = ' . $id);
        return $query->getOneOrNullResult();
    }

    public function getAllWithCategories()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT a,c FROM AppBundle:Product a
                              JOIN a.category c
                              WHERE a.isActive = 1");
        return $query->getResult();
    }

    public function count(): int
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT COUNT(a.id) c 
                                        FROM AppBundle:Product a
                                        JOIN a.category ca
                                        WHERE a.isActive = 1");

        return intval($query->getOneOrNullResult()['c']);
    }

    public function findAllPerPage(int $limit = 0, int $offset = 0)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT a,c FROM AppBundle:Product a
                                       JOIN a.category c
                                       WHERE a.isActive = 1
                                       ORDER BY a.price ASC");

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function findByUserPaginated(int $limit = 0,int $offset = 0,int $userId){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT a,c FROM AppBundle:Product a
                                       JOIN a.category c
                                       WHERE a.isActive = 1
                                       AND a.owner = $userId
                                       ORDER BY a.price ASC");

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function getUserProductsCount(int $userId){

        $db = $this->getEntityManager()->getConnection();
        $result = $db->fetchColumn("SELECT COUNT(p.id)
                                             FROM products as p
                                             WHERE p.status = 1
                                             AND p.user_id = $userId");
        return $result;
    }
}
