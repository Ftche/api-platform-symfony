<?php

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiAuthGroups
{

    public function __construct(public $groups)
    {
    }
}