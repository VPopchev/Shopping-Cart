<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:29 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
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
        $this->addToCategoryPromotions($product);
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
        $this->addToCategoryPromotions($product);
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

    private function addToCategoryPromotions(Product $product)
    {
        /** @var Promotion $promotion */
        $promotion = $product->getTopPromotion();
        if(null != $promotion){
            $promotion->removeProduct($product);
        }
        $category = $product->getCategory();
        /** @var Category $parent */
        $parent = $category->getParent();
        if (null !== $parent){
            $categoryId = $parent->getId();
        } else {
            $categoryId = $category->getId();
        }
        $categoryPromos = $this
            ->promotionRepository
            ->findCategoryPromotion($categoryId);
        /** @var Promotion $promo */
        foreach ($categoryPromos as $promo){
            $promo->addProduct($product);
        }
        $this->entityManager->flush();
    }

    public function findByUserPaginated(int $limit, int $offset, int $userId)
    {
        return $this
                    ->productRepository
                    ->findByUserPaginated($limit,$offset,$userId);
    }

    public function getUserProductsCount(int $userId)
    {
        return $this->productRepository->getUserProductsCount($userId);
    }

    public function addComment(Product $product, string $content, $user)
    {
        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setProduct($product);
        $comment->setContent($content);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}