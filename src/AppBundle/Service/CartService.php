<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 17:51 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Cart;
use AppBundle\Entity\User;
use AppBundle\Repository\PromotionRepository;
use Doctrine\ORM\EntityManager;

class CartService implements CartServiceInterface
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
     * CartService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager,
                                PromotionRepository $promotionRepository)
    {
        $this->entityManager = $entityManager;
        $this->promotionRepository = $promotionRepository;
    }


    public function cashOut(User $user)
    {
        /** @var Cart $userCart */
        $userCart = $user->getCart();
        foreach ($userCart->getProducts() as $product) {
            $productTopPromo = $product->getTopPromotion();
            $productOwner = $product->getOwner();
            $productOwner->increaseCash($productTopPromo == null ?
                $product->getPrice() : $product->getPromoPrice());
            $product->setOwner($user);
            $this->entityManager->merge($productOwner);
        }
        $user->decreaseCash($userCart->getTotal($user));
        $this->clearCart($user);
        $this->entityManager->merge($user);
        $this->entityManager->flush();
    }

    public function clearCart(User $user)
    {
        /** @var Cart $userCart */
        $userCart = $user->getCart();
        $userCart->clear();
        $this->entityManager->merge($userCart);
        $this->entityManager->flush();
    }
}