<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\DDC2504;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "root"  = "DDC2504RootClass",
 *     "child" = "DDC2504ChildClass"
 * })
 */
class DDC2504RootClass
{
    /**
     * @Column(type="integer")
     * @Id @GeneratedValue
     */
    public $id;

    /**
     * @var \DarkWebDesign\DoctrineUnitTesting\Models\DDC2504\DDC2504OtherClass
     *
     * @ManyToOne(targetEntity="DDC2504OtherClass", inversedBy="childClasses")
     */
    public $other;
}
