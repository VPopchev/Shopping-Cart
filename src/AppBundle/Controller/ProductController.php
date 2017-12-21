<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Shipper;
use AppBundle\Entity\User;
use AppBundle\Form\ProductType;
use AppBundle\Form\ShipperType;
use AppBundle\Service\ProductServiceInterface;
use AppBundle\Service\ShipperServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var ShipperServiceInterface
     */
    private $shipperService;

    /**
     * ProductController constructor.
     * @param ProductServiceInterface $productService
     * @param ShipperServiceInterface $shipperService
     */
    public function __construct(ProductServiceInterface $productService,
                                ShipperServiceInterface $shipperService)
    {
        $this->productService = $productService;
        $this->shipperService = $shipperService;
    }


    /**
     * @Route("product/create", name="create_product")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function createAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productService->create($product, $this->getUser());
            $this->addFlash('success',
                "Product {$product->getName()} added successful!");
            return $this->redirectToRoute('user_profile');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("product/view/{id}",name="view_product")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Product $product,Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser != null &&
            (!$currentUser->isEditor() &&
                !$currentUser->isAdmin() &&
                !$currentUser->isOwner($product)) &&
            $product->getIsActive() == false
        ) {
            return $this->redirectToRoute('homepage');
        }
        $shipper = new Shipper();
        $form = $this->createForm(ShipperType::class,$shipper);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                $userCart = $currentUser->getCart();
                $this->shipperService->createShipper($shipper,$userCart,$product);
                $this->addFlash('success', "{$product->getName()} added to cart successfully!");
                return $this->redirectToRoute('view_product',[
                    'id' => $product->getId(),
                ]);
        }
        return $this->render('product/view.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }




    /**
     * @Route("product/edit/{id}",name="edit_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Product $product, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin()
            && !$currentUser->isEditor()
        ) {
            return $this->redirectToRoute("homepage");
        }

        $baseImage = $product->getImage();
        $product->setImage(New File('./app/images/' . $product->getImage()));

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productService->edit($product, $baseImage);
            $this->addFlash('success',
                "Product {$product->getName()} successful edited!");
            return $this->redirectToRoute('view_product',[
                'id' => $product->getId()
            ]);
        }
        return $this->render('product/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("product/delete/{id}",name="delete_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Product $product, Request $request)
    {
        $product->setImage(New File('./app/images/' . $product->getImage()));
        $currentUser = $this->getUser();
        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin() && !$currentUser->isEditor()) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->productService->delete($product);
            $this->addFlash('success', "Product {$product->getName()} successful deleted!");
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('product/delete.html.twig', ['form' => $form->createView()]);
    }
}
