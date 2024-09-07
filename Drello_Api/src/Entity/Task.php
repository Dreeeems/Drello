<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Projects $project = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $creator = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tasks')]
    private Collection $assigned;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Teams $team = null; // Renommé de team_id à team

    public function __construct()
    {
        $this->assigned = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getProject(): ?Projects
    {
        return $this->project;
    }

    public function setProject(?Projects $project): static
    {
        $this->project = $project;
        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAssigned(): Collection
    {
        return $this->assigned;
    }

    public function addAssigned(User $assigned): static
    {
        if (!$this->assigned->contains($assigned)) {
            $this->assigned->add($assigned);
        }
        return $this;
    }

    public function removeAssigned(User $assigned): static
    {
        $this->assigned->removeElement($assigned);
        return $this;
    }

    public function getTeam(): ?Teams // Renommé de getTeamId à getTeam
    {
        return $this->team;
    }

    public function setTeam(?Teams $team): static // Renommé de setTeamId à setTeam
    {
        $this->team = $team;
        return $this;
    }
}
