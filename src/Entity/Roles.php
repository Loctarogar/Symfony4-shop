<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RolesRepository")
 */
class Roles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserGroupes", mappedBy="role")
     */
    private $userGroupes;

    public function __construct()
    {
        $this->userGroupes = new ArrayCollection();
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
     * @return Collection|UserGroupes[]
     */
    public function getUserGroupes(): Collection
    {
        return $this->userGroupes;
    }

    public function addUserGroupe(UserGroupes $userGroupe): self
    {
        if (!$this->userGroupes->contains($userGroupe)) {
            $this->userGroupes[] = $userGroupe;
            $userGroupe->setRole($this);
        }

        return $this;
    }

    public function removeUserGroupe(UserGroupes $userGroupe): self
    {
        if ($this->userGroupes->contains($userGroupe)) {
            $this->userGroupes->removeElement($userGroupe);
            // set the owning side to null (unless already changed)
            if ($userGroupe->getRole() === $this) {
                $userGroupe->setRole(null);
            }
        }

        return $this;
    }
}
