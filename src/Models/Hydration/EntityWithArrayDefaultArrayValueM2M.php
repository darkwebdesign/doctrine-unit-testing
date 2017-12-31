<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Hydration;

/** @Entity */
class EntityWithArrayDefaultArrayValueM2M
{
    /** @Id @Column(type="integer") @GeneratedValue(strategy="AUTO") */
    public $id;

    /** @ManyToMany(targetEntity=SimpleEntity::class) */
    public $collection = [];
}
