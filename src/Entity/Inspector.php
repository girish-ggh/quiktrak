<?php

namespace App\Entity;

use App\Repository\InspectorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InspectorRepository::class)]
class Inspector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('UK', 'Mexico', 'India')")]
    private ?string $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getTimeZone(): string
    {
        return match ($this->location) {
            'UK' => 'Europe/London',
            'Mexico' => 'America/Mexico_City',
            'India' => 'Asia/Kolkata',
            default => 'UTC',
        };
    }
}
