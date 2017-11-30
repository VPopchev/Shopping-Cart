<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
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
            'user' => $user,
            'categories' => $this->categories,
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
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->createCart($user);
            return $this->redirectToRoute('security_login');
        }
        foreach($form->getErrors(true) as $error){
            $this->addFlash('error',$error->getMessage());
        }
        return $this->render('user/register.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("user/userProfileView/{id}",name="view_user_profile")
     */
    public function viewUserProfile(User $user){
        return $this->render('user/userProfileView.html.twig',
            ['user' => $user,'categories' => $this->categories]);
    }




    private function createCart($user)
    {
        $cart = new Cart();
        $cart->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();
    }
}
