<?php

namespace Dmishh\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'dmishh_settings', indexes: [new ORM\Index(name: 'name_owner_id_idx', columns: ['name', 'owner_id'])])]
#[ORM\Entity]
class Setting
{

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $value;

    #[ORM\Column(name: 'owner_id', type: 'string', length: 255, nullable: true)]
    private $ownerId;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setName(?string $name): void
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
    
    public function setOwnerId(?string $ownerId): void
    {
        $this->ownerId = $ownerId;
    }
}
