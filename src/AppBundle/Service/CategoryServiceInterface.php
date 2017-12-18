<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 18.12.2017 г.
 * Time: 13:17 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Category;
use AppBundle\Entity\Promotion;

interface CategoryServiceInterface
{
    public function create(Category $category);

    public function listCategoryProducts(int $limit,int $offset,$categoryId);

    public function getProductsCount(int $categoryId);

    public function delete(Category $category);

    public function addCategoryToPromotion(int $categoryId,Promotion $promotion);

    public function getAllProducts(int $categoryId);

    public function getAllCategories();

    public function find(int $categoryId);
}