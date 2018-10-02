<?php

namespace App\Controllers;
use Slim\Http\UploadedFile;

class Controller
{

    protected $container;

    protected $timeStamp;

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function __construct($container)
    {
        try {
            $this->timeStamp = new \DateTime();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

        $this->container = $container;
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}
