<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

class DependencyDataPersister implements ContextAwareDataPersisterInterface
{

    private DependencyRepository $repository;

    public function __construct(DependencyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Dependency;
    }

    public function persist($data, array $context = [])
    {
        $this->repository->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->repository->remove($data);
    }
}