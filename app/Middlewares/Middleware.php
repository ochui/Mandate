<?php

namespace App\Middlewares;

class Middleware
{

    protected $container;


    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function __construct($container)
    {
        $this->container = $container;
    }
}
