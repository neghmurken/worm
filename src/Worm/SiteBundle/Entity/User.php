<?php

namespace Worm\SiteBundle\Entity;

use FOS\UserBundle\Entity\User as FOSUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Worm\SiteBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="ws_user")
 */
class User extends FOSUser
{

    /**
     * @ORM\Id;
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="author")
     */
    protected $submissions;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {

        return $this->id;
    }
}