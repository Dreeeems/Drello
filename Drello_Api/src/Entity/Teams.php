<?php

namespace App\Entity;

use App\Repository\TeamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams')]
    private Collection $users;

    /**
     * @var Collection<int, Projects>
     */
    #[ORM\OneToMany(targetEntity: Projects::class, mappedBy: 'team')]
    private Collection $projects;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: "team_admins")]
    private Collection $admins;

    #[ORM\ManyToOne(inversedBy: 'created_teams')]
    private ?User $creator = null;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->admins = new ArrayCollection();
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
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Projects>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Projects $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setTeam($this);
        }

        return $this;
    }

    public function removeProject(Projects $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getTeam() === $this) {
                $project->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of admins
     */ 
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Set the value of admins
     *
     * @return  self
     */ 
    public function setAdmins($admins)
    {
        $this->admins = $admins;

        return $this;
    }

    public function addAdmin(User $user): static
    {
        if (!$this->admins->contains($user)) {
            $this->admins->add($user);
        }
        return $this;
    }

    public function removeAdmin(User $user): static
    {
        $this->admins->removeElement($user);
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
}
