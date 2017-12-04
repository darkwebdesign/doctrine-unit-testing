<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\JoinedInheritanceType;

/**
 * @Entity
 * @InheritanceType("JOINED")
 */
class RootClass
{
    /**
     * @Column(type="integer")
     * @Id @GeneratedValue
     */
    public $id;
}