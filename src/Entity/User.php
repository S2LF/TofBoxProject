<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo_profil;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Chat", mappedBy="Users")
     */
    private $chats;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="User")
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $Comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Follow", mappedBy="user")
     */
    private $Following;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Follow", mappedBy="userFollowed")
     */
    private $Followed;

    public function __construct()
    {
        $this->chats = new ArrayCollection();
        $this->Comments = new ArrayCollection();
        $this->Following = new ArrayCollection();
        $this->Followed = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photo_profil;
    }

    public function setPhotoProfil(?string $photo_profil): self
    {
        $this->photo_profil = $photo_profil;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->addUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            $chat->removeUser($this);
        }

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->contains($comment)) {
            $this->Comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Follow[]
     */
    public function getFollowing(): Collection
    {
        return $this->Following;
    }

    public function addFollowing(Follow $following): self
    {
        if (!$this->Following->contains($following)) {
            $this->Following[] = $following;
            $following->setUser($this);
        }

        return $this;
    }

    public function removeFollowing(Follow $following): self
    {
        if ($this->Following->contains($following)) {
            $this->Following->removeElement($following);
            // set the owning side to null (unless already changed)
            if ($following->getUser() === $this) {
                $following->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Follow[]
     */
    public function getFollowed(): Collection
    {
        return $this->Followed;
    }

    public function addFollowed(Follow $followed): self
    {
        if (!$this->Followed->contains($followed)) {
            $this->Followed[] = $followed;
            $followed->setUserFollowed($this);
        }

        return $this;
    }

    public function removeFollowed(Follow $followed): self
    {
        if ($this->Followed->contains($followed)) {
            $this->Followed->removeElement($followed);
            // set the owning side to null (unless already changed)
            if ($followed->getUserFollowed() === $this) {
                $followed->setUserFollowed(null);
            }
        }

        return $this;
    }
}
