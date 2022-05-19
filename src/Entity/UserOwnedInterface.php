<?php

namespace App\Entity;

interface UserOwnedInterface
{

    public function getUser(): ?User;

    public function setUser(?User $user): self;
}