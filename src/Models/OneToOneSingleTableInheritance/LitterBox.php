<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\OneToOneSingleTableInheritance;

/**
 * @Entity
 * @Table(name="one_to_one_single_table_inheritance_litter_box")
 */
class LitterBox
{
    /** @Id @Column(type="integer") @GeneratedValue(strategy="AUTO") */
    public $id;
}
