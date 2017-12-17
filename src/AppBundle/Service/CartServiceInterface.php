<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 17:50 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;

interface CartServiceInterface
{
    public function cashOut(User $user);

    public function clearCart(User $user);
}