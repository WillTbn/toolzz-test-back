<?php

namespace App\Services;

use ReflectionObject;
use ReflectionProperty;

abstract class Service
{

    /**
     * Abstract method to force all services to implement its logic here
     */
    abstract public function execute();

    /**
     * List all public attributes
     *
     * @return array
     */
    public function json(): array
    {
        $results = [];

        $reflectionObject = (new ReflectionObject($this));
        $properties = $reflectionObject->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $results[$property->getName()] = $property->getValue($this);
        }

        return $results;
    }

}
