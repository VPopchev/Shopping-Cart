<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:58 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class PromotionService implements PromotionServiceInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PromotionRepository
     */
    private $promotionRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * PromotionService constructor.
     * @param PromotionRepository $promotionRepository
     * @param UserRepository $userRepository
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param EntityManager $entityManager
     */
    public function __construct(PromotionRepository $promotionRepository,
                                UserRepository $userRepository,
                                ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                EntityManager $entityManager)
    {
        $this->promotionRepository = $promotionRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    public function addUserToPromotions(User $user)
    {
        $productPromotions = $this->promotionRepository->findProductAndCategoryPromotions();
        /** @var Promotion $promotion */
        foreach ($productPromotions as $promotion) {
            $promotion->addUser($user);
            $this->entityManager->merge($promotion);
        }
        $this->entityManager->flush();
    }

    public function setRichUsersToPromo(Promotion $promotion)
    {
        $users = $this->userRepository->findRichUsers();
        $products = $this->productRepository->findAllActive();
        foreach ($products as $product) {
            $promotion->addProduct($product);
        }
        foreach ($users as $user) {
            $promotion->addUser($user);
        }
        return $promotion;
    }

    public function setAllUsersToPromo(Promotion $promotion)
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $promotion->addUser($user);
        }
        return $promotion;
    }

    public function updateUserPromotions()
    {
        $userTypePromos = $this->promotionRepository->findUserTypePromotions();
        $richUsers = $this->userRepository->findRichUsers();
        /** @var Promotion $promo */
        foreach ($userTypePromos as $promo) {
            $promo->setUsers($richUsers);
        }
        $this->entityManager->flush();
    }

    public function addCategoryToPromotion(int $categoryId,Promotion $promotion)
    {
        $products = $this->categoryRepository->findAllProducts($categoryId);
        /** @var Category $category */
        $category = $this->categoryRepository->find($categoryId);
        $promotion->addCategory($category);
        foreach ($products as $product){
            $promotion->addProduct($product);
        }
        $this->entityManager->merge($promotion);
        $this->entityManager->flush();
    }


    public function removeCategoryFromPromotion(int $categoryId,Promotion $promotion)
    {
        $products = $this->categoryRepository->findAllProducts($categoryId);
        $promotion->setCategories(new ArrayCollection());
        foreach ($products as $product){
            $promotion->removeProduct($product);
        }
        $this->entityManager->merge($promotion);
        $this->entityManager->flush();
    }

    public function addProductToPromotion(Promotion $promotion,Product $product)
    {
        if(!$promotion->getProducts()->contains($product)){
            $promotion->addProduct($product);
        }
        $this->entityManager->persist($promotion);
        $this->entityManager->flush();
    }

    public function removeProductFromPromotion(Promotion $promotion, Product $product)
    {
        $promotion->removeProduct($product);
        $this->entityManager->persist($promotion);
        $this->entityManager->flush();
    }

    public function addUserToPromo(Promotion $promotion, int $userId)
    {
        $user = $this->userRepository->find($userId);
        if (!$promotion->getUsers()->contains($user)){
            $promotion->addUser($user);
        }
        $this->entityManager->merge($promotion);
        $this->entityManager->flush();
    }

    public function removeUserFromPromo(Promotion $promotion, int $userId)
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        $promotion->removeUser($user);
        $this->entityManager->merge($promotion);
        $this->entityManager->flush();
    }
}