<?php

namespace App\Controllers;

class Controller
{

    protected $container;

    protected $timeStamp;


    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function __construct($container)
    {
        try {
            $this->timeStamp = new \DateTime();
        }catch(\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
        
        $this->container = $container;
    }

}
