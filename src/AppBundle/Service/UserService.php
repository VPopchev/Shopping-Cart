<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 16.12.2017 г.
 * Time: 15:49 ч.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;
use AppBundle\Repository\PromotionRepository;
use AppBundle\Repository\RoleRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserService implements UserServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;


    /**
     * UserService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager,
                                UserRepository $userRepository,
                                RoleRepository $roleRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }


    public function register(User $user)
    {
        $password = password_hash($user->getPassword(),PASSWORD_BCRYPT);
        $user->setPassword($password);
        $userRole = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $user->addRole($userRole);
        $user->setCart($this->createCart());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function createCart()
    {
        $cart = new Cart();
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $cart;
    }


    public function findAll()
    {
        return $this->userRepository->findAll();
    }
}