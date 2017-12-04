<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Issue5989;

/**
 * @Entity
 * @Table(name="issue5989_managers")
 */
class Issue5989Manager extends Issue5989Person
{
    /**
     * @Column(type="simple_array", nullable=true)
     *
     * @var array
     */
    public $tags;
}
