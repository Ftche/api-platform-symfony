<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;

class PostCountController
{
    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request): int
    {
        $onlineQuery = $request->get('online');
        $conditioins = [];
        if ($conditioins != null) {
            $conditioins = ['online' => $onlineQuery === '1' ? true : false];
        }
        return $this->postRepository->count([$conditioins]);
    }
}