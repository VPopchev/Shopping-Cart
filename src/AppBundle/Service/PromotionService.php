<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:58 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\UserRepository;
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
     * PromotionService constructor.
     * @param PromotionRepository $promotionRepository
     * @param UserRepository $userRepository
     * @param ProductRepository $productRepository
     * @param EntityManager $entityManager
     */
    public function __construct(PromotionRepository $promotionRepository,
                                UserRepository $userRepository,
                                ProductRepository $productRepository,
                                EntityManager $entityManager)
    {
        $this->promotionRepository = $promotionRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    public function addUserToPromotions(User $user)
    {
        $productPromotions = $this->promotionRepository->findProductTypePromotions();
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
        foreach ($richUsers as $user) {
            /** @var Promotion $promo */
            foreach ($userTypePromos as $promo) {
                if ($promo->getUsers()->contains($user)) {
                    continue;
                }
                $promo->addUser($user);
            }
        }
        $this->entityManager->flush();
    }
}