<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\OneToOneSingleTableInheritance;

/**
 * @Entity
 * @Table(name="one_to_one_single_table_inheritance_pet")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorMap({"cat" = "Cat"})
 */
abstract class Pet
{
    /** @Id @Column(type="integer") @GeneratedValue(strategy="AUTO") */
    public $id;
}
