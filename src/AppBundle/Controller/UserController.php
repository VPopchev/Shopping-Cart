<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("user/profile", name="user_profile")
     */
    public function profileAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('user/profile.html.twig', array(
            'user' => $user
        ));
    }


    /**
     * @Route("user/register", name="register_action")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRegistrationDate(new \DateTime());
        $user->setCash(1500);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveUser($user);
            return $this->redirectToRoute('security_login');
        }
        $errors = $form->getErrors(true);
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    private function saveUser(User $user)
    {
        $password = $this->get('security.password_encoder')
                         ->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $roleRepository = $this->getDoctrine()->getRepository(Role::class);
        $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $user->addRole($userRole);
        $user->setCart($this->createCart());
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();
    }

    /**
     * @Route("user/userProfileView/{id}",name="view_user_profile")
     */
    public function viewUserProfile(User $user)
    {
        return $this->render('user/userProfileView.html.twig',
            ['user' => $user]);
    }

    private function createCart()
    {
        $cart = new Cart();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();
        return $cart;
    }
}
