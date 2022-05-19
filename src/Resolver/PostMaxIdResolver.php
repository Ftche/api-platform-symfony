<?php

namespace App\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Repository\PostRepository;

class PostMaxIdResolver implements QueryCollectionResolverInterface
{

    public function __construct(private PostRepository $postRepository)
    {

    }

    public function __invoke(iterable $collection, array $context): iterable
    {
        return $this->postRepository->findWithMaxId($context['args']['id']);
    }
}