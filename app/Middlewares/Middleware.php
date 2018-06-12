<?php

namespace App\Middlewares;

class Middleware
{

    protected $container;


    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    /**
     * Middleware constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
}
