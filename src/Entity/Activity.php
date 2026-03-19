<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'activity')]
    private Collection $users;

    /**
     * @var Collection<int, Services>
     */
    #[ORM\OneToMany(targetEntity: Services::class, mappedBy: 'activity')]
    private Collection $Services;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->Services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setActivity($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getActivity() === $this) {
                $user->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Services>
     */
    public function getServices(): Collection
    {
        return $this->Services;
    }

    public function addService(Services $service): static
    {
        if (!$this->Services->contains($service)) {
            $this->Services->add($service);
            $service->setActivity($this);
        }

        return $this;
    }

    public function removeService(Services $service): static
    {
        if ($this->Services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getActivity() === $this) {
                $service->setActivity(null);
            }
        }

        return $this;
    }
}
