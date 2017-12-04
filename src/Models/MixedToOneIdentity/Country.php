<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\MixedToOneIdentity;

/** @Entity */
class Country
{
    /** @Id @Column(type="string") @GeneratedValue(strategy="NONE") */
    public $country;
}
