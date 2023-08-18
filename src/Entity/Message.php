<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Le message ne peut pas être vide.")]
    private ?string $content;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    private ?string $title;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "sentMessages")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "receivedMessages")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }
}
