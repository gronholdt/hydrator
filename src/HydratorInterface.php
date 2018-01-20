<?php


namespace SolBianca\Hydrator;


/**
 * Hydrator can be used for two purposes:
 *
 * - To extract data from a class to be futher stored in a persistent storage.
 * - To instantiate a class having its data.
 *
 * In both cases it is saving and filling protected and private non static properties without calling
 * any methods which leads to ability to persist state of an object with properly incapsulated
 * data.
 */
interface HydratorInterface
{

    /**
     * Creates an instance of a class filled with data according to map
     *
     * @param string|object $object Object or class name to hydrate
     * @param array $data Array represented as ['propertyName' => 'propertyValue]
     * @return object Object with filling properties
     *
     * @throws HydratorException Not valid class name
     * @throws HydrateObjectException iObject don't have property by given array
     */
    public function hydrate($object, array $data);

    /**
     * Extracts data from an object according to map
     *
     * @param object $object Object you need to extract data
     * @param array $properties Properties for extraction. If not set extract data from all properties
     * @return array
     */
    public function extract($object, array $properties = []);
}
