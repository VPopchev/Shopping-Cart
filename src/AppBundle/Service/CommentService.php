<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 21.12.2017 г.
 * Time: 10:10 ч.
 */

namespace AppBundle\Service;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;

class CommentService implements CommentServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * CommentService constructor.
     * @param EntityManager $entityManager
     * @param CommentRepository $commentRepository
     */
    public function __construct(EntityManager $entityManager,
                                CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
    }


    public function addComment(Product $product, string $content, $user)
    {
        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setProduct($product);
        $comment->setContent($content);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function deleteComment(Comment $comment)
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }
}