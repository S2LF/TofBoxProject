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
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="photos")
     */
    private $categories;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="photo", cascade={"remove"})
     */
    private $Comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="likes")
     */
    private $likeUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="photo")
     */
    private $reports;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->User = new ArrayCollection();
        $this->Comments = new ArrayCollection();
        $this->likeUsers = new ArrayCollection();
        $this->reports = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Collection|LikeUser[]
     */
    public function getLikeUsers(): Collection
    {
        return $this->likeUsers;
    }

    public function addLikeUser(User $likeUsers): self
    {
        if (!$this->likeUsers->contains($likeUsers)) {
            $this->likeUsers[] = $likeUsers;
            $likeUsers->addLike($this);
        }

        return $this;
    }

    public function removeLikeUser(User $likeUsers): self
    {
        if ($this->likeUsers->contains($likeUsers)) {
            $this->likeUsers->removeElement($likeUsers);
            $likeUsers->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setPhoto($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getPhoto() === $this) {
                $report->setPhoto(null);
            }
        }

        return $this;
    }


}
