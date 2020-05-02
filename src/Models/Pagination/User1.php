<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Pagination;

/**
 * Class User1
 * @package DarkWebDesign\DoctrineUnitTesting\Models\Pagination
 *
 * @Entity()
 */
class User1 extends User
{
    /**
     * @Column(type="string")
     */
    public $email;
}
