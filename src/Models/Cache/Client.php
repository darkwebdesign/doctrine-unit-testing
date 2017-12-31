<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Cache;

/**
 * @Entity
 * @Table("cache_client")
 */
class Client
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(unique=true)
     */
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
