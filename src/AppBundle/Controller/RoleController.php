<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editRoles(User $user){
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$currentUser->isAdmin() && !$currentUser->isEditor()){
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(UserType::class,$user);
        return $this->render('user/roleChange.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("user/roleChange/{id}",name="edit_roles_action")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editRolesAction(User $user,Request $request){
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $roleName = $request->request->get('role');

        if (null === $roleName){
            $user->removeRoles();
            $em->merge($user);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        if(in_array($roleName,$user->getRoles())){
            $this->addFlash('error',
                'User is already' . explode('_',$roleName)[1]);
            return $this->redirectToRoute('edit_roles',['id' => $user->getId()]);
        }

        $role = $this->getDoctrine()->getRepository(Role::class)
            ->findOneBy(['name' => $roleName]);
        $role->addUser($user);
        $user->addRole($role);
        $em->merge($user);
        $em->flush();
        $this->addFlash('success',
            "User {$user->getUsername()} roles edited successful!");
        return $this->redirectToRoute('homepage');
    }
}
