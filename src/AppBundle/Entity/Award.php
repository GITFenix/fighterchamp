<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="award")
 */
class Award
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var UserFight
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserFight", inversedBy="awards")
     */
    private $userFight;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    public function __construct(UserFight $userFight, string $description)
    {
        $this->userFight = $userFight;
        $this->name = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserFight(): UserFight
    {
        return $this->userFight;
    }

    public function getName(): string
    {
        return $this->name;
    }
}