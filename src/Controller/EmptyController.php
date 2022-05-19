<?php

namespace App\Controller;

class EmptyController
{
public function __invoke($data)
{
    return $data;
}
}