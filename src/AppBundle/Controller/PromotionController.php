<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Promotion;
use AppBundle\Form\PromotionType;
use AppBundle\Service\CategoryServiceInterface;
use AppBundle\Service\PromotionServiceInterface;
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
     * PromotionController constructor.
     * @param PromotionServiceInterface $promotionService
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(PromotionServiceInterface $promotionService,
                                CategoryServiceInterface $categoryService)
    {
        $this->promotionService = $promotionService;
        $this->categoryService = $categoryService;
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
            if ($promotion->getType() == 2) {
                $this->promotionService->setRichUsersToPromo($promotion);
            } else if ($promotion->getType() == 1) {
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
     * @param Promotion $promotion
     * @param int $categoryId
     * @Route("promotion/addCategory/{id}/{categoryId}",name="category_to_promotion")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function categoryToPromoAction(Promotion $promotion, int $categoryId){
        $this->categoryService->addCategoryToPromotion($categoryId, $promotion);
        return $this->redirectToRoute('promotion_list');
    }

    /**
     *
     * @Route("promotion/choiceProducts/{id}/{categoryId}",name="product_to_promotion")
     * @param Promotion $promotion
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function choiceProducts(Promotion $promotion, $categoryId)
    {
        $category = $this->categoryService->find($categoryId);
        $products = $this->categoryService->getAllProducts($categoryId);
        return $this->render('promotion/addProducts.html.twig', [
            'promotion' => $promotion,
            'products' => $products,
            'category' => $category
        ]);
    }

}
