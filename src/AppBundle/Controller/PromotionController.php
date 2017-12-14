<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\User;
use AppBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends Controller
{
    const PRODUCTS_LIMIT = 6;


    /**
     * Lists all promotion entities.
     *
     * @Route("/promotions/list", name="promotion_list")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $promotions = $em->getRepository('AppBundle:Promotion')->findAll();

        return $this->render('promotion/listAll.html.twig', array(
            'promotions' => $promotions,
        ));
    }

    /**
     * Creates a new promotion entity.
     *
     * @Route("promotion/new", name="promotion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($promotion->getType() == 2){
                $this->setPromotionToUsers($promotion);
            } else if ($promotion->getType() == 1){
                $this->setUsersToPromotion($promotion);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            return $this->redirectToRoute('promotion_show', array('id' => $promotion->getId()));
        }

        return $this->render('promotion/new.html.twig', array(
            'promotion' => $promotion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a promotion entity.
     *
     * @Route("promotion/view/{id}", name="promotion_show")
     * @Method("GET")
     */
    public function showAction(Promotion $promotion)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->getCategoriesWithProducts();
        return $this->render('promotion/view.html.twig', array(
            'promotion' => $promotion,
            'categories' => $categories
        ));
    }

    /**
     * @Route("promotion/edit/{id}", name="promotion_edit")
     */
    public function editAction(Request $request, Promotion $promotion)
    {
        $editForm = $this->createForm('AppBundle\Form\PromotionType', $promotion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('promotion_list');
        }

        return $this->render('promotion/edit.html.twig', array(
            'promotion' => $promotion,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a promotion entity.
     *
     * @Route("/promotion/delete/{id}", name="promotion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Promotion $promotion)
    {
        $form = $this->createDeleteForm($promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($promotion);
            $em->flush();
        }

        return $this->redirectToRoute('promotion_list');
    }

    /**
     * @param Promotion $promotion
     */
    private function createDeleteForm(Promotion $promotion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('promotion_delete', array('id' => $promotion->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @param int $productId
     * @Route("promotion/choiceProducts/{id}/{categoryId}",name="product_to_promotion")
     */
    public function choiceProducts(Promotion $promotion,$categoryId){
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        $products = [];
        $this->recursion($category,$products);
        return $this->render('promotion/addProducts.html.twig',[
            'promotion' => $promotion,
            'products' => $products,
        ]);
    }

    /**
     * @param int $promotionId
     * @param int $productId
     * @Route("promotion/insertProduct/{promotionId}/{productId}", name="add_product_to_promotion")
     */
    public function addProduct(int $promotionId,int $productId){
        /** @var Promotion $promotion */
        $promotion = $this->getDoctrine()->getRepository(Promotion::class)->find($promotionId);
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);
        $promotion->addProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($promotion);
        $em->flush();
        if ($product->getCategory()->getParent()){
            $categoryId = $product->getCategory()->getParent()->getId();
        } else {
            $categoryId = $product->getCategory()->getId();
        }
        return $this->redirectToRoute('product_to_promotion',[
            'id' => $promotion->getId(),
            'categoryId' => $categoryId
        ]);
    }


    private function recursion(Category $category,&$products){
        foreach ($category->getActiveProducts() as $activeProd){
            array_push($products,$activeProd);
        }
        if ($category->getChildren()){
            foreach($category->getChildren() as $child){
                $this->recursion($child,$products);
            }
        }
    }

    public function setPromotionToUsers(Promotion $promotion){
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findByCash();
        $products = $this->getDoctrine()->getRepository(Product::class)
            ->findAll();
        foreach ($products as $product){
            $promotion->addProduct($product);
        }
        foreach($users as $user){
            $promotion->addUser($user);
        }
        return $promotion;
    }

    private function setUsersToPromotion(Promotion $promotion)
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();
        foreach ($users as $user){
            $promotion->addUser($user);
        }
        return $promotion;
    }
}
