<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\Quote;

/**
 * @Entity
 * @Table(name="`quote-address`")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({"simple" = Address::class, "full" = FullAddress::class})
 */
class Address
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="`address-id`")
     */
    public $id;

    /**
     * @Column(name="`address-zip`")
     */
    public $zip;

    /**
     * @OneToOne(targetEntity="User", inversedBy="address")
     * @JoinColumn(name="`user-id`", referencedColumnName="`user-id`")
     */
    public $user;


    public function setUser(User $user) {
        if ($this->user !== $user) {
            $this->user = $user;
            $user->setAddress($this);
        }
    }


    public function getId()
    {
        return $this->id;
    }

    public function getZip()
    {
        return $this->zip;
    }

    public function getUser()
    {
        return $this->user;
    }

}
