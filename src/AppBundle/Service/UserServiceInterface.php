<?php
namespace AppBundle\Service;


use AppBundle\Entity\User;

interface UserServiceInterface
{
    public function register(User $user);

    public function findAll();
}