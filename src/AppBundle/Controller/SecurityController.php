<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("user/login", name="security_login")
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        return $this->render('user/login.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("user/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }

}
