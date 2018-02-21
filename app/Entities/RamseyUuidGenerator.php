<?php
namespace App\Entities;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

final class RamseyUuidGenerator implements UuidGenerator
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function toString()
    {   
        $methodName = 'uuid' . $this->version;
        $uuid = Uuid::$methodName();

        return $uuid->toString();
    }   
}

