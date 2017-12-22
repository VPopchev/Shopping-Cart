<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Form\PromotionType;
use AppBundle\Service\CategoryServiceInterface;
use AppBundle\Service\PromotionServiceInterface;
use AppBundle\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends Controller
{
    const PRODUCTS_LIMIT = 6;

    /**
     * @var PromotionServiceInterface
     */
    private $promotionService;

    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * PromotionController constructor.
     * @param PromotionServiceInterface $promotionService
     * @param CategoryServiceInterface $categoryService
     * @param UserServiceInterface $userService
     */
    public function __construct(PromotionServiceInterface $promotionService,
                                CategoryServiceInterface $categoryService,
                                UserServiceInterface $userService)
    {
        $this->promotionService = $promotionService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
    }


    /**
     * @Route("/promotion/list", name="promotion_list")
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
     * @Route("promotion/new", name="promotion_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($promotion->getType() == 'user') {
                $this->promotionService->setRichUsersToPromo($promotion);
            }
            else if ($promotion->getType() == 'product' ||
                     $promotion->getType() == 'category' )
            {
                $this->promotionService->setAllUsersToPromo($promotion);
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
     * @Route("promotion/view/{id}", name="promotion_show")
     * @Method("GET")
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Promotion $promotion)
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->getAllCategoriesWithProducts();
        return $this->render('promotion/view.html.twig', array(
            'promotion' => $promotion,
            'categories' => $categories
        ));
    }

    /**
     * @Route("promotion/edit/{id}", name="promotion_edit")
     * @param Request $request
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * @param int $promotionId
     * @param int $productId
     * @Route("promotion/insertProduct/{promotionId}/{productId}", name="add_product_to_promotion")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addProductAction(int $promotionId, int $productId)
    {
        /** @var Promotion $promotion */
        $promotion = $this->getDoctrine()->getRepository(Promotion::class)->find($promotionId);
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);
        $this->promotionService->addProductToPromotion($promotion,$product);

        return $this->redirectToRoute('product_to_promotion', [
            'id' => $promotion->getId(),
            'categoryId' => $product->getCategory()->getId()
        ]);
    }

    /**
     * @param int $promotionId
     * @param int $productId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("promotion/removeProduct/{promotionId}/{productId}", name="remove_product_from_promotion")
     */
    public function removeProductAction(int $promotionId, int $productId)
    {
        /** @var Promotion $promotion */
        $promotion = $this->getDoctrine()->getRepository(Promotion::class)->find($promotionId);
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);
        $this->promotionService->removeProductFromPromotion($promotion,$product);

        return $this->redirectToRoute('product_to_promotion', [
            'id' => $promotion->getId(),
            'categoryId' => $product->getCategory()->getId()
        ]);
    }

    /**
     * @param Promotion $promotion
     * @param int $categoryId
     * @Route("promotion/addCategory/{id}/{categoryId}",name="category_to_promotion")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function categoryToPromoAction(Promotion $promotion, int $categoryId){
        $this->promotionService->addCategoryToPromotion($categoryId, $promotion);

        return $this->redirectToRoute('promotion_show',[
            'id' => $promotion->getId()
        ]);
    }

    /**
     * @param Promotion $promotion
     * @param int $categoryId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("promotion/removeCategory/{id}/{categoryId}",name="remove_category_from_promo")
     */
    public function removeCategoryFromPromotion(Promotion $promotion,int $categoryId){
        $this->promotionService->removeCategoryFromPromotion($categoryId,$promotion);
        return $this->redirectToRoute('promotion_show',[
            'id' => $promotion->getId()
        ]);
    }

    /**
     *
     * @Route("promotion/choiceProducts/{id}/{categoryId}",name="product_to_promotion")
     * @param Promotion $promotion
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function choiceProductsAction(Promotion $promotion, $categoryId)
    {
        $category = $this->categoryService->find($categoryId);
        $products = $this->categoryService->getAllProducts($categoryId);
        return $this->render('promotion/addProducts.html.twig', [
            'promotion' => $promotion,
            'products' => $products,
            'category' => $category
        ]);
    }

    /**
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("promotion/choiceUsers/{id}",name="user_to_promotion")
     */
    public function choiceUsersAction(Promotion $promotion){
        $users = $this->userService->findAll();
        return $this->render('promotion/addUsers.html.twig',[
            'users' => $users,
            'promotion' => $promotion
        ]);
    }

    /**
     * @param Promotion $promotion
     * @param int $userId
     * @Route("promotion/addUser/{id}/{userId}",name="add_user_to_promo")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addUserToPromotions(Promotion $promotion,int $userId){
        $this->promotionService->addUserToPromo($promotion,$userId);
        return $this->redirectToRoute('user_to_promotion',[
            'id' => $promotion->getId()
        ]);
    }

    /**
     * @param Promotion $promotion
     * @param int $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("promotion/removeUser/{id}/{userId}",name="remove_user_from_promo")
     */
    public function removeUserFromPromotions(Promotion $promotion,int $userId){
        $this->promotionService->removeUserFromPromo($promotion,$userId);
        return $this->redirectToRoute('user_to_promotion',[
            'id' => $promotion->getId()
        ]);
    }

    /**
     * @param Promotion $promotion
     * @Route("promotion/delete/{id}",name="delete_promotion")
     */
    public function deletePromotionAction(Promotion $promotion){
        $this->promotionService->removePromotion($promotion);
        $this->addFlash('success','Promotion Deleted Successful');
        return $this->redirectToRoute('promotion_list');
    }
}
