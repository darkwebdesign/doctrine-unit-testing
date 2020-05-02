<?php

declare(strict_types=1);

namespace DarkWebDesign\DoctrineUnitTesting\Models\ValueConversionType;

/**
 * @Entity
 * @Table(name="vct_owning_manytoone_foreignkey")
 */
class OwningManyToOneIdForeignKeyEntity
{
    /**
     * @Id
     * @ManyToOne(targetEntity=AuxiliaryEntity::class, inversedBy="associatedEntities")
     * @JoinColumn(name="associated_id", referencedColumnName="id4")
     */
    public $associatedEntity;
}
