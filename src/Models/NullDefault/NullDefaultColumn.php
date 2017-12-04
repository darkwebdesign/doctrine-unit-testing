<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\NullDefault;

/** @Entity */
class NullDefaultColumn
{
    /** @Id @GeneratedValue @Column(type="integer") */
    public $id;

    /** @Column(options={"default":NULL}) */
    public $nullDefault;
}
