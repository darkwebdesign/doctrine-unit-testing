<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\DDC3699;

/** @MappedSuperclass */
abstract class DDC3699Parent
{
    const CLASSNAME = __CLASS__;

    /** @Column(type="string") */
    public $parentField;
}