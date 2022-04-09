<?php

namespace Dmishh\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="dmishh_settings", indexes={@ORM\Index(name="name_owner_id_idx", columns={"name", "owner_id"})})
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @var string|null
     *
     * @ORM\Column(name="owner_id", type="string", length=255, nullable=true)
     */
    private $ownerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getOwnerId(): ?string
    {
        return $this->ownerId;
    }

    public function setOwnerId(?string $ownerId)
    {
        $this->ownerId = $ownerId;
    }
}