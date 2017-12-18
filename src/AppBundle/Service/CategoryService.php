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
use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;


    public function __construct(CategoryRepository $categoryRepository,
                                EntityManager $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }


    public function create(Category $category)
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function listCategoryProducts(int $limit,int $offset, $categoryId)
    {
        return $this->categoryRepository->findProductsByCategoryPaginated($categoryId,$limit,$offset);
    }

    public function getProductsCount(int $categoryId)
    {
        return $this->categoryRepository->getProductsCountByCategory($categoryId);
    }

    public function delete(Category $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    public function addCategoryToPromotion(int $categoryId,Promotion $promotion)
    {
        $products = $this->categoryRepository->findAllProducts($categoryId);
        foreach ($products as $product){
            $promotion->addProduct($product);
            $this->entityManager->merge($promotion);
        }
        $this->entityManager->flush();
    }

    public function getAllProducts(int $categoryId){
        return $this->categoryRepository->findAllProducts($categoryId);
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }

    public function find(int $categoryId)
    {
        return $this->categoryRepository->find($categoryId);
    }
}