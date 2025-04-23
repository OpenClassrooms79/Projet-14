<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Mapping\ManyToMany;

#[Entity]
#[Table('`tag`')]
class Tag
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column(unique: true)]
    #[Slug(fields: ['name'])]
    private string $code;

    #[NotBlank]
    #[Length(max: 30)]
    #[Column(length: 30)]
    private string $name;

    /**
     * @var Collection<int, VideoGame>
     */
    #[ManyToMany(targetEntity: VideoGame::class, mappedBy: 'tags')]
    private Collection $videoGames;

    public function __construct()
    {
        $this->videoGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Tag
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Tag
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, VideoGame>
     */
    public function getVideoGames(): Collection
    {
        return $this->videoGames;
    }

    public function addVideoGame(VideoGame $videoGame): static
    {
        if (!$this->videoGames->contains($videoGame)) {
            $this->videoGames->add($videoGame);
            $videoGame->addTag($this);
        }

        return $this;
    }

    public function removeVideoGame(VideoGame $videoGame): static
    {
        if ($this->videoGames->removeElement($videoGame)) {
            $videoGame->removeTag($this);
        }

        return $this;
    }
}
