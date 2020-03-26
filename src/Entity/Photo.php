<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nb_like;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_update;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="photos")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="photo")
     */
    private $User;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="photo")
     */
    private $Comments;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->User = new ArrayCollection();
        $this->Comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getNbLike(): ?int
    {
        return $this->nb_like;
    }

    public function setNbLike(?int $nb_like): self
    {
        $this->nb_like = $nb_like;

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

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategories(Category $category): self
    {
        if (!$this->categories->contains($categories)) {
            $this->categories[] = $categories;
        }

        return $this;
    }

    public function removeCategories(Category $categories): self
    {
        if ($this->categories->contains($categories)) {
            $this->categories->removeElement($categories);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setPhoto($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->contains($user)) {
            $this->User->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getPhoto() === $this) {
                $user->setPhoto(null);
            }
        }

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
            $comment->setPhoto($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->contains($comment)) {
            $this->Comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPhoto() === $this) {
                $comment->setPhoto(null);
            }
        }

        return $this;
    }
}
