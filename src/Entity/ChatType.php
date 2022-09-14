<?php

namespace App\Entity;

use App\Repository\ChatTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ChatType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $alias = null;

    #[ORM\Column]
    private ?int $MessageTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getMessageTime(): ?int
    {
        return $this->MessageTime;
    }

    public function setMessageTime(int $MessageTime): self
    {
        $this->MessageTime = $MessageTime;

        return $this;
    }

    #[ORM\PrePersist]
    public function asd()
    {
        $this->MessageTime = $this->MessageTime ?? time();
    }

    public function __toString(): string
    {
        return '';
    }
}
