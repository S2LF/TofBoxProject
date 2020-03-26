<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowRepository")
 */
class Follow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_follow;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Following")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Followed")
     */
    private $userFollowed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFollow(): ?\DateTimeInterface
    {
        return $this->date_follow;
    }

    public function setDateFollow(\DateTimeInterface $date_follow): self
    {
        $this->date_follow = $date_follow;

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

    public function getUserFollowed(): ?User
    {
        return $this->userFollowed;
    }

    public function setUserFollowed(?User $userFollowed): self
    {
        $this->userFollowed = $userFollowed;

        return $this;
    }

}
