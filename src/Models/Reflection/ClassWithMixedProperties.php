<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Reflection;

class ClassWithMixedProperties extends ParentClass
{
    public static $staticProperty = 'staticProperty';

    public $publicProperty = 'publicProperty';

    protected $protectedProperty = 'protectedProperty';

    private $privateProperty = 'privateProperty';

    private $privatePropertyOverride = 'privatePropertyOverride';
}
