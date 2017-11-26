<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        return $this->render('user/login.html.twig', [
            'categories' => $this->categories
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }

}
