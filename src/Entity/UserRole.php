<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRoleRepository")
 */
class UserRole
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
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userRoles")
     */
    private $app_user;

    public function __construct()
    {
        $this->app_user = new ArrayCollection();
    }

    public function __toString()
    {
        $a = $this->name;

        return (string) $a;
    }

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

    /**
     * @return Collection|User[]
     */
    public function getAppUser(): Collection
    {
        return $this->app_user;
    }

    public function addAppUser(User $appUser): self
    {
        if (!$this->app_user->contains($appUser)) {
            $this->app_user[] = $appUser;
        }

        return $this;
    }

    public function removeAppUser(User $appUser): self
    {
        if ($this->app_user->contains($appUser)) {
            $this->app_user->removeElement($appUser);
        }

        return $this;
    }
}
