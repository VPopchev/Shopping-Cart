<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shipper;
use AppBundle\Entity\User;
use AppBundle\Service\CartServiceInterface;
use AppBundle\Service\PromotionServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CartController extends Controller
{

    /**
     * @var CartServiceInterface
     */
    private $cartService;

    /**
     * @var PromotionServiceInterface
     */
    private $promotionService;

    /**
     * CartController constructor.
     * @param CartServiceInterface $cartService
     * @param PromotionServiceInterface $promotionService
     */
    public function __construct(CartServiceInterface $cartService,
                                PromotionServiceInterface $promotionService)
    {
        $this->cartService = $cartService;
        $this->promotionService = $promotionService;
    }


    /**
     * @Route("product/add/{id}",name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Product $product,Request $request)
    {
        /** @var Cart $userCart */
        $userCart = $this->getUser()->getCart();

        $quantity = $request->request->get('quantity');

        $em = $this->getDoctrine()->getManager();
        $shipper = new Shipper();
        $shipper->setQuantity($quantity);
        $shipper->setCart($userCart);
        $shipper->setProduct($product);

        $em->persist($shipper);
        $em->flush();
        $this->addFlash('success', "{$product->getName()} added to cart successfully!");
        return $this->redirectToRoute('view_product', [
            'id' => $product->getId()
        ]);
    }

    /**
     * @Route("product/remove/{id}",name="remove_from_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Product $product)
    {
        /** User $user */
        $user = $this->getUser();
        /** @var Cart $userCart */
        $userCart = $this->getUser()->getCart();
        $userShipper = $this->getDoctrine()->getRepository(Shipper::class)
            ->findOneBy(['product' => $product,'cart' => $userCart]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($userShipper);
        $em->flush();
        $this->addFlash('success', "{$product->getName()} was removed from cart!");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("product/clearCart",name="clear_cart")
     */
    public function clearCart()
    {
        $user = $this->getUser();
        $this->cartService->clearCart($user);
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
        $this->cartService->cashOut($user);
        $this->promotionService->updateUserPromotions();
        $this->addFlash('success', "CashOut successful");
        return $this->redirectToRoute('user_profile');
    }
}
