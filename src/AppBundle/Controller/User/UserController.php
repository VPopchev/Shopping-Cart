<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Product;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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
        $user->setCash(1500.00);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $roleRepository = $this->getDoctrine()
                                    ->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('security_login');
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->categories
        ]);
    }
}
