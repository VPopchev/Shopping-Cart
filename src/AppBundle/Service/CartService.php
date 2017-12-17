<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 17:51 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shipper;
use AppBundle\Entity\User;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\ShipperRepository;
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
     * @var ShipperRepository
     */
    private $shipperRepository;

    /**
     * CartService constructor.
     * @param EntityManager $entityManager
     * @param PromotionRepository $promotionRepository
     * @param ShipperRepository $shipperRepository
     */
    public function __construct(EntityManager $entityManager,
                                PromotionRepository $promotionRepository,
                                ShipperRepository $shipperRepository)
    {
        $this->entityManager = $entityManager;
        $this->promotionRepository = $promotionRepository;
        $this->shipperRepository = $shipperRepository;
    }


    public function cashOut(User $user)
    {
        /** @var Shipper $shipper */
        $userCart = $user->getCart();
        foreach ($userCart->getShipper() as $shipper) {
            $product = $shipper->getProduct();
            $productTopPromo = $product->getTopPromotion();
            /** @var User $productOwner */
            $productOwner = $product->getOwner();
            $productOwner->increaseCash($productTopPromo == null ?
                $product->getPrice() : $product->getPromoPrice());
            $newProduct = $this->createNewProd($user, $product);
            $newProduct->setQuantity($shipper->getQuantity());
            $product->decreaseQuantity($shipper->getQuantity());
            $this->entityManager->persist($newProduct);
            $this->entityManager->merge($product);
            $this->entityManager->flush();
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
        $userShippers = $this->shipperRepository->findBy(['cart' => $userCart]);
        foreach ($userShippers as $userShipper) {
            $this->entityManager->remove($userShipper);
        }
        $this->entityManager->merge($userCart);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param $product
     * @return Product
     */
    private function createNewProd(User $user,Product $product): Product
    {
        $newProduct = new Product();
        $newProduct->setName($product->getName());
        $newProduct->setDescription($product->getDescription());
        $newProduct->setCategory($product->getCategory());
        $newProduct->setIsActive(0);
        $newProduct->setImage($product->getImage());
        $newProduct->setPrice($product->getPrice());
        $newProduct->setOwner($user);
        return $newProduct;
    }
}