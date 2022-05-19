<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class PostImageController
{
    public function __invoke(Request $request): Post
    {

        $post = $request->attributes->get('data');
        //dd($post);
        if(!$post ){
            throw new \RuntimeException('Article entendu');
        }
        $post->setFile($request->files->get('file'));
        $post->setUpdatedAt(new \DateTimeImmutable());
        return $post;
    }
}