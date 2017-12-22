<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Service\CommentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @var CommentServiceInterface
     */
    private $commentService;

    /**
     * CommentController constructor.
     * @param CommentServiceInterface $commentService
     */
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param Product $product
     * @param Request $request
     * @Route("comment/add/{id}",name="comment_product")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function commentAction(Product $product, Request $request){
        $content = $request->request->get('content');
        $author = $this->getUser();
        $this->commentService->addComment($product,$content,$author);
        return $this->redirectToRoute('view_product',[
            'id' => $product->getId()
        ]);
    }

    /**
     * @param Comment $comment
     * @Route("comment/delete/{id}",name="delete_comment")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function deleteAction(Comment $comment){
        $this->commentService->deleteComment($comment);
        $this->addFlash('success','Comment Deleted Successful!');
        return $this->redirectToRoute('view_product',[
            'id' =>$comment->getProduct()->getId()
        ]);
    }

}
