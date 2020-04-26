<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowRepository")
 * @ORM\Table(name="follow",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="follow_unique",
 *          columns={"followed_users_id", "follow_by_users_id"})
 *          })
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followedUsers")
     */
    private $followedUsers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followByUsers")
     */
    private $followByUsers;

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

    public function getFollowedUsers(): ?User
    {
        return $this->followedUsers;
    }

    public function setFollowedUsers(?User $followedUsers): self
    {
        $this->followedUsers = $followedUsers;

        return $this;
    }

    public function getFollowByUsers(): ?User
    {
        return $this->followByUsers;
    }

    public function setFollowByUsers(?User $followByUsers): self
    {
        $this->followByUsers = $followByUsers;

        return $this;
    }



}
