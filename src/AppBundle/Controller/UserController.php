<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shipper;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\PromotionServiceInterface;
use AppBundle\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var PromotionServiceInterface
     */
    private $promotionService;


    public function __construct(UserServiceInterface $userService,
                                PromotionServiceInterface $promotionService)
    {
        $this->userService = $userService;
        $this->promotionService = $promotionService;
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("user/profile", name="user_profile")
     */
    public function profileAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $userCart = $user->getCart();
        $cartShippers = $this->getDoctrine()
            ->getRepository(Shipper::class)
            ->findBy(['cart' => $userCart]);
        return $this->render('user/profile.html.twig', array(
            'user' => $user,
            'shippers' => $cartShippers
        ));
    }


    /**
     * @Route("user/register", name="register_action")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRegistrationDate(new \DateTime());
        $user->setCash(1500);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->register($user);
            $this->promotionService->addUserToPromotions($user);
            return $this->redirectToRoute('security_login');
        }
        $errors = $form->getErrors(true);
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("user/userProfileView/{id}",name="view_user_profile")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserProfile(User $user)
    {
        return $this->render('user/userProfileView.html.twig',
            ['user' => $user]);
    }
}
