<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Cache;

/**
 * @Entity
 * @Table("cache_attraction_contact_info")
 */
class AttractionContactInfo extends AttractionInfo
{
    /**
     * @Column(unique=true)
     */
    protected $fone;

    public function __construct($fone, Attraction $attraction)
    {
        $this->setAttraction($attraction);
        $this->setFone($fone);
    }

    public function getFone()
    {
        return $this->fone;
    }

    public function setFone($fone)
    {
        $this->fone = $fone;
    }
}
