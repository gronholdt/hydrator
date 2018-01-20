<?php


namespace SolBianca\Hydrator;


class Hydrator implements HydratorInterface
{

    /**
     * Local cache of reflection class instances
     * @var array
     */
    private $reflectionClassMap = [];

    /**
     * {@inheritdoc}
     */
    public function hydrate($object, array $data)
    {
        if (is_object($object)) {
            $className = get_class($object);
            $reflection = $this->getReflectionClass($className);
        } elseif (is_string($object)) {
            if (!class_exists($object)) {
                throw new HydrateObjectException("Given class name '{$object}' must be valid class name.");
            }
            $className = $object;
            $reflection = $this->getReflectionClass($className);
            $object = $reflection->newInstanceWithoutConstructor();
        } else {
            throw new HydrateObjectException("Invalid object.");
        }
        foreach ($data as $propertyName => $propertyValue) {
            if (!$reflection->hasProperty($propertyName)) {
                throw new HydrateObjectException("There's no '$propertyName' property in '$className'.");
            }
            $property = $reflection->getProperty($propertyName);
            if ($property->isStatic()) {
                continue;
            }
            $property->setAccessible(true);
            $property->setValue($object, $propertyValue);
        }
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($object, array $properties = [])
    {
        $data = [];
        $className = get_class($object);
        $reflection = $this->getReflectionClass($className);
        if ([] === $properties) {
            $properties = $this->getReflectionProperties($reflection);
        }
        foreach ($properties as $propertyName) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                if ($property->isStatic()) {
                    continue;
                }
                $property->setAccessible(true);
                $data[$propertyName] = $property->getValue($object);
            } else {
                throw new ExctractObjectException("There's no '$propertyName' property in '$className'.");
            }
        }
        return $data;
    }

    /**
     * @param \ReflectionClass $reflection
     * @return array
     */
    protected function getReflectionProperties(\ReflectionClass $reflection)
    {
        $properties = $reflection->getProperties();
        $result = [];
        if (empty($properties)) {
            return $result;
        }

        foreach ($properties as $property) {
            $result[] = $property->getName();
        }
        return $result;
    }

    /**
     * Returns instance of reflection class for class name passed
     *
     * @param string $className
     * @return \ReflectionClass
     */
    protected function getReflectionClass($className)
    {
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }
        return $this->reflectionClassMap[$className];
    }
}
