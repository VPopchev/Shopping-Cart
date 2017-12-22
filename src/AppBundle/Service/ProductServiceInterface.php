<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:29 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Product;
use AppBundle\Entity\User;

interface ProductServiceInterface
{
    public function edit(Product $product,$baseImage);

    public function create(Product $product,User $user);

    public function delete(Product $product);

    public function findByUserPaginated(int $limit,int $offset,int $userId);

    public function getUserProductsCount(int $userId);


}