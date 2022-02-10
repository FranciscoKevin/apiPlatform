<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext: [
        "groups" => ["read:collection"],
        "openapi_definition_name" => "Collection"
    ],
    denormalizationContext: ["groups" => ["write:Post"]],
    paginationItemsPerPage: 2,
    paginationMaximumItemsPerPage: 2,
    paginationClientItemsPerPage: true,
    collectionOperations: [
        "get",
        "post",
        "count" => [
            "method" => "GET",
            "path" => "/post/count",
            "controller" => PostCountController::class,
            "read" => false,
            "pagination_enabled" => false,
            "filters" => [],
            "openapi_context" => [
                "summary" => "Récupère le nombre total d'article",
                "parameters" => [
                    [
                        "in" => "query",
                        "name" => "isOnline",
                        "schema" => [
                            "type" => "integer",
                            "maximum" => 1,
                            "minimum" => 0
                        ],
                        "description" => "Filtre les articles en ligne"
                    ]
                ],
                "responses" => [
                    "200" => [
                        "description" => "OK",
                        "content" => [
                            "application/json" => [
                                "schema" => [
                                    "type" => "integer",
                                    "exemple" => 2
                                ]
                            ]
                        ] 
                    ]
                ]
            ]
        ]
    ],
    itemOperations: [
        "put",
        "delete",
        "get" => [
            "normalization_context" => [
                "groups" => ["read:collection", "read:item", "read:Post"],
                "openapi_definition_name" => "Detail"
            ]
        ],
        "publish" => [
            "method" => "POST",
            "path" => "/post/{id}/publish",
            "controller" => PostPublishController::class,
            "openapi_context" => [
                "summary" => "Permet de publier un article",
                "requestBoby" => [
                    "content" => [
                        "application/json" => [
                            "schema" => []
                        ]
                    ]
                ]
            ]
        ]
    ]
),
ApiFilter(SearchFilter::class, properties: ["id" => "exact", "title" => "partial"])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read:collection"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(["read:collection", "write:Post"]),
        Length(min:5, groups: ["create:Post"])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read:collection", "write:Post"])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(["read:item", "write:Post"])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["read:item"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts', cascade:['persist'])]
    #[
        Groups(["read:item", "write:Post"]),
        Valid()
    ]
    private $category;

    #[ORM\Column(type: 'boolean', options:["default" => 0])]
    #[Groups(["read:collection"])]
    private $isOnline = false;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): self
    {
        $this->isOnline = $isOnline;

        return $this;
    }
}
