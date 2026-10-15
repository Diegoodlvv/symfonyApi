<?php

namespace App\Entity;

use App\Repository\VideoGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: VideoGameRepository::class)]
class VideoGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]     
    #[Groups(['videogame:read', 'videogame:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['videogame:read', 'videogame:write'])]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['videogame:read', 'videogame:write'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'videoGames')]
    #[Groups(['videogame:read'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: Category::class,
                groups: ['category:read'],
                name: 'CategoryRead'
            )
        )
    )]
    private Collection $category;

    #[ORM\ManyToOne(inversedBy: 'videoGames')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['videogame:read'])]
    #[OA\Property(
        ref: new Model(
            type: Editor::class,
            groups: ['editor:read'],
            name: 'EditorRead'
        )
    )]
    private ?Editor $editor = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['videogame:read', 'videogame:write'])]
    private ?string $coverImage = null;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

}
