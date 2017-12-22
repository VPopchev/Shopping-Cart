<?php

namespace AppBundle\Service;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;

interface CommentServiceInterface
{
    public function addComment(Product $product,string $content,$user);

    public function deleteComment(Comment $comment);
}