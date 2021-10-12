<?php

namespace App\Controller;

use App\Entity\Post;

class PostPublishController
{
    public function __invoke(Post $data): Post
    {
        $data->setOnline(true);
        return $data;
    }
}