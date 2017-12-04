<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Quote;

/**
 * @Entity
 */
class FullAddress extends Address
{
    /**
     * @OneToOne(targetEntity=City::class, cascade={"persist"})
     * @JoinColumn(name="`city-id`", referencedColumnName="`city-id`")
     *
     * @var City
     */
    public $city;
}
