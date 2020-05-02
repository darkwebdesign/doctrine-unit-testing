<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\CustomType;

use DarkWebDesign\DoctrineUnitTesting\DbalTypes\CustomIdObject;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="custom_id_type_parent")
 */
class CustomIdObjectTypeParent
{
    /**
     * @Id @Column(type="CustomIdObject")
     *
     * @var CustomIdObject
     */
    public $id;

    /**
     * @OneToMany(targetEntity="DarkWebDesign\DoctrineUnitTesting\Models\CustomType\CustomIdObjectTypeChild", cascade={"persist", "remove"}, mappedBy="parent")
     */
    public $children;

    /**
     * @param CustomIdObject $id
     */
    public function __construct(CustomIdObject $id)
    {
        $this->id       = $id;
        $this->children = new ArrayCollection();
    }
}
