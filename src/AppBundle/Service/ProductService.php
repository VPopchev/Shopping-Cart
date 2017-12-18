<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:29 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\PromotionRepository;
use Doctrine\ORM\EntityManager;

class ProductService implements ProductServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var PromotionRepository
     */
    private $promotionRepository;

    /**
     * ProductService constructor.
     * @param EntityManager $entityManager
     * @param $productRepository
     */
    public function __construct(EntityManager $entityManager,
                                ProductRepository $productRepository,
                                PromotionRepository $promotionRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
    }


    public function edit(Product $product,$baseImage)
    {
        $this->setImage($product,$baseImage);
        $this->entityManager->flush();
    }

    public function create(Product $product,User $user)
    {
        $file = $product->getImage();
        $fileUploader = new FileUploader('app/images');
        $fileName = $fileUploader->upload($file);
        $product->setImage($fileName);
        $product->setOwner($user);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->addToUserTypePromotions($product);
    }

    private function addToUserTypePromotions($product){
        $userPromos = $this->promotionRepository->findUserTypePromotions();
        /** @var Promotion $promo */
        foreach ($userPromos as $promo){
            $promo->addProduct($product);
            $this->entityManager->merge($promo);
        }
        $this->entityManager->flush();
    }

    private function setImage(Product $product,$baseImage){
        if (null === $product->getImage()) {
            $product->setImage($baseImage);
        } else {
            $file = $product->getImage();
            $fileUploader = new FileUploader('app/images');
            $fileName = $fileUploader->upload($file);
            $product->setImage($fileName);
        }
    }

    public function delete(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}