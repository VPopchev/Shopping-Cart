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

interface PromotionServiceInterface
{
    public function setRichUsersToPromo(Promotion $promotion);

    public function setAllUsersToPromo(Promotion $promotion);

    public function addUserToPromotions(User $user);

    public function updateUserPromotions();
}