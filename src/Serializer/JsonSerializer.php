<?php

namespace RayRutjes\DomainFoundation\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;

class JsonSerializer implements Serializer
{
    /**
     * @param $object
     *
     * @return mixed
     */
    public function serialize(Serializable $object)
    {
        $reflectionClass = new \ReflectionClass($object);
        $data = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }
            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($object);
        }

        return json_encode($data);
    }

    /**
     * @param          $object
     * @param Contract $type
     *
     * @return Serializable
     */
    public function deserialize($object, Contract $type)
    {
        $reflectionClass = new \ReflectionClass($type->className());
        $data = json_decode($object, true);
        $instance = $reflectionClass->newInstanceWithoutConstructor();
        foreach ($data as $propertyName => $propertyValue) {
            if (!$reflectionClass->hasProperty($propertyName)) {
                continue;
            }
            $reflectionProperty = $reflectionClass->getProperty($propertyName);
            if (!$reflectionProperty->isPublic()) {
                $reflectionProperty->setAccessible(true);
            }
            $reflectionProperty->setValue($instance, $propertyValue);
        }

        return $instance;
    }
}
