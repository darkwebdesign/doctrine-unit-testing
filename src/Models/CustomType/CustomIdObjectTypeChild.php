<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\CustomType;

use DarkWebDesign\DoctrineUnitTesting\DbalTypes\CustomIdObject;

/**
 * @Entity
 * @Table(name="custom_id_type_child")
 */
class CustomIdObjectTypeChild
{
    /**
     * @Id @Column(type="CustomIdObject")
     *
     * @var CustomIdObject
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomIdObjectTypeParent", inversedBy="children")
     */
    public $parent;

    /**
     * @param CustomIdObject           $id
     * @param CustomIdObjectTypeParent $parent
     */
    public function __construct(CustomIdObject $id, CustomIdObjectTypeParent $parent)
    {
        $this->id     = $id;
        $this->parent = $parent;
    }
}
