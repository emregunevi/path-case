<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userName;

    /**
     * @var string The hashed Password
     * @ORM\Column(type="string", length=255)
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user",orphanRemoval=true)
     */
    private $userOrders;

    public function __construct()
    {
        $this->userOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getUserOrders(): Collection
    {
        return $this->userOrders;
    }

    public function addUserOrder(Order $userOrder): self
    {
        if (!$this->userOrders->contains($userOrder)) {
            $this->userOrders[] = $userOrder;
            $userOrder->setUser($this);
        }

        return $this;
    }

    public function removeUserOrder(Order $userOrder): self
    {
        if ($this->userOrders->removeElement($userOrder)) {
            // set the owning side to null (unless already changed)
            if ($userOrder->getUser() === $this) {
                $userOrder->setUser(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'fullName' => $this->getFullName(),
            'userName' => $this->getUserName(),
            'password' => $this->getPassword(),
            'createdAt' => $this->getCreatedAt()->format('d.m.Y H:i:s')
        ];
    }
}
