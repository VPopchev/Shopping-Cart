<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class RoleController extends Controller
{
    /**
     * @Route("user/roleChange/{id}",name="edit_roles")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRoles(User $user){
        $form = $this->createForm(UserType::class,$user);
        return $this->render('user/roleChange.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("user/roleChange/{id}",name="edit_roles_action")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editRolesAction(User $user,Request $request){
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $roleName = $request->request->get('role');

        if (null === $roleName){
            $this->removeRoles($user,$em);
            return $this->redirectToRoute('view_user_profile',['id' => $user->getId()]);
        }

        if(!in_array($roleName,$user->getRoles())){
            $this->addRole($user,$roleName,$em);
        }
        return $this->redirectToRoute('view_user_profile',['id' => $user->getId()]);
    }

    private function removeRoles(User $user,ObjectManager $em)
    {
        $user->removeRoles();
        $em->merge($user);
        $em->flush();
        return $this->redirectToRoute('homepage');
    }

    private function addRole(User $user,$roleName,ObjectManager $em)
    {
        $role = $this->getDoctrine()->getRepository(Role::class)
            ->findOneBy(['name' => $roleName]);
        $role->addUser($user);
        $user->addRole($role);
        $em->merge($user);
        $em->flush();
        $this->addFlash('success',
            "User {$user->getUsername()} roles edited successful!");
    }
}
