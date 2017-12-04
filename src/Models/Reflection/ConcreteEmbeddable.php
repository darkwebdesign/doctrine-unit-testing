<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Reflection;

/**
 * A test asset used to check that embeddables support properties defined in abstract classes
 */
class ConcreteEmbeddable extends AbstractEmbeddable
{
    private $propertyInConcreteClass;
}
