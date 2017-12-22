<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 16:58 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;

interface PromotionServiceInterface
{
    public function setRichUsersToPromo(Promotion $promotion);

    public function setAllUsersToPromo(Promotion $promotion);

    public function addUserToPromotions(User $user);

    public function addUserToPromo(Promotion $promotion,int $userId);

    public function removeUserFromPromo(Promotion $promotion,int $userId);

    public function updateUserPromotions();

    public function addCategoryToPromotion(int $categoryId,Promotion $promotion);

    public function removeCategoryFromPromotion(int $categoryId,Promotion $promotion);

    public function addProductToPromotion(Promotion $promotion,Product $product);

    public function removeProductFromPromotion(Promotion $promotion,Product $product);

    public function removePromotion(Promotion $promotion);
}