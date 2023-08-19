<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rank = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $summonerName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Message::class)]
    private Collection $receivedMessages;

    #[ORM\ManyToMany(targetEntity: Games::class, inversedBy: 'users')]
    private Collection $myGames;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: FriendRequest::class)]
    private Collection $sentFriendRequests;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: FriendRequest::class)]
    private Collection $receivedFriendRequests;

    #[ORM\OneToMany(mappedBy: 'user1', targetEntity: Conversation::class)]
    private Collection $conversationsAsUser1;

    #[ORM\OneToMany(mappedBy: 'user2', targetEntity: Conversation::class)]
    private Collection $conversationsAsUser2;

    public function __construct()
    {
        $this->myGames = new ArrayCollection();
        $this->sentFriendRequests = new ArrayCollection();
        $this->receivedFriendRequests = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->conversationsAsUser1 = new ArrayCollection();
        $this->conversationsAsUser2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAge(): \DateTimeInterface
    {
        return $this->age;
    }

    public function setAge(?\DateTimeInterface $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getSummonerName(): ?string
    {
        return $this->summonerName;
    }

    public function setSummonerName(?string $summonerName): static
    {
        $this->summonerName = $summonerName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Games>
     */
    public function getMyGames(): Collection
    {
        return $this->myGames;
    }

    public function addMyGame(Games $myGame): static
    {
        if (!$this->myGames->contains($myGame)) {
            $this->myGames->add($myGame);
        }

        return $this;
    }

    public function removeMyGame(Games $myGame): static
    {
        $this->myGames->removeElement($myGame);

        return $this;
    }

    public function getFriends(): Collection
    {
        $friends = new ArrayCollection();

        foreach ($this->receivedFriendRequests as $friendRequest) {
            if ($friendRequest->getAccepted()) {
                $friends->add($friendRequest->getSender());
            }
        }

        foreach ($this->sentFriendRequests as $friendRequest) {
            if ($friendRequest->getAccepted()) {
                $friends->add($friendRequest->getReceiver());
            }
        }

        return $friends;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function acceptFriendRequest(FriendRequest $friendRequest): static
    {
        if ($this === $friendRequest->getReceiver()) {
            $friendRequest->setAccepted(true);
        }

        return $this;
    }

    public function rejectFriendRequest(FriendRequest $friendRequest): static
    {
        if ($this === $friendRequest->getReceiver()) {
            $this->receivedFriendRequests->removeElement($friendRequest);
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->sentFriendRequests;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getReceivedFriendRequests(): Collection
    {
        return $this->receivedFriendRequests;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $message): static
    {
        if (!$this->sentMessages->contains($message)) {
            $this->sentMessages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $message): static
    {
        if ($this->sentMessages->contains($message)) {
            $this->sentMessages->removeElement($message);

            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $message): static
    {
        if (!$this->receivedMessages->contains($message)) {
            $this->receivedMessages->add($message);
            $message->setRecipient($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $message): static
    {
        if ($this->receivedMessages->contains($message)) {
            $this->receivedMessages->removeElement($message);

            if ($message->getRecipient() === $this) {
                $message->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsAsUser1(): Collection
    {
        return $this->conversationsAsUser1;
    }

    public function addConversationAsUser1(Conversation $conversation): static
    {
        if (!$this->conversationsAsUser1->contains($conversation)) {
            $this->conversationsAsUser1->add($conversation);
            $conversation->setUser1($this);
        }

        return $this;
    }

    public function removeConversationAsUser1(Conversation $conversation): static
    {
        if ($this->conversationsAsUser1->removeElement($conversation)) {
            if ($conversation->getUser1() === $this) {
                $conversation->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsAsUser2(): Collection
    {
        return $this->conversationsAsUser2;
    }

    public function addConversationAsUser2(Conversation $conversation): static
    {
        if (!$this->conversationsAsUser2->contains($conversation)) {
            $this->conversationsAsUser2->add($conversation);
            $conversation->setUser2($this);
        }

        return $this;
    }

    public function removeConversationAsUser2(Conversation $conversation): static
    {
        if ($this->conversationsAsUser2->removeElement($conversation)) {
            if ($conversation->getUser2() === $this) {
                $conversation->setUser2(null);
            }
        }

        return $this;
    }
}
