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
        return $this->container->{$property} ? $this->container->{$property} : null;
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
