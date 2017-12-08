<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CartController extends Controller
{
    /**
     * @Route("product/add/{id}",name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addAction(Product $product, Request $request)
    {
        /** @var Cart $userCart */
        $userCart = $this->getUser()->getCart();
        if ($userCart->getProducts()->contains($product)) {
            $this->addFlash('error', 'You already added this product to cart!');
            return $this->redirectToRoute("view_product",
                ['product' => $product, 'id' => $product->getId()]);
        }
        $em = $this->getDoctrine()->getManager();
        $userCart->addProduct($product);

        $em->persist($userCart);
        $em->flush();
        $this->addFlash('success', "{$product->getName()} added to cart successfully!");
        return $this->redirectToRoute('view_product', [
            'id' => $product->getId()
        ]);
    }

    /**
     * @Route("product/remove/{id}",name="remove_from_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function removeAction(Product $product)
    {
        /** @var Cart $userCart */
        $userCart = $this->getUser()->getCart();
        $userCart->getProducts()->removeElement($product);
        $em = $this->getDoctrine()->getManager();
        $em->merge($userCart);
        $em->flush();
        $this->addFlash('success', "{$product->getName()} was removed from cart!");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("product/clearCart",name="clear_cart")
     */
    public function clearCart()
    {
        /** @var Cart $userCart */
        $userCart = $this->getUser()->getCart();
        $userCart->clearCart();
        $em = $this->getDoctrine()->getManager();
        $em->merge($userCart);
        $em->flush();
        return $this->redirectToRoute('user_profile');
    }


    /**
     * @Route("product/cashOut",name="cash_out_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cashOutAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Cart $userCart */
        $userCart = $user->getCart();
        $em = $this->getDoctrine()->getManager();
        foreach ($userCart->getProducts() as $product) {
            $productOwner = $product->getOwner();
            $productOwner->setCash($product->getPrice());
            $product->setOwner($user);
            $em->merge($productOwner);
        }
        $user->decreaseCash($userCart->getTotal());
        $this->clearCart();
        $em->merge($user);
        $em->flush();
        $this->addFlash('success', "CashOut successful");
        return $this->redirectToRoute('user_profile');
    }
}
