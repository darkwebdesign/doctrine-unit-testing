<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\DDC753;

/**
 * @Entity()
 */
class DDC753EntityWithDefaultCustomRepository
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @column(type="string") */
    protected $name;

}
