<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostPublishController;
use App\Controller\PostCountController;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"read:collection"}, "openapi_definition_name"="Collection"},
 *     denormalizationContext={"groups"={"write:item","write:Post"}},
 *     collectionOperations={
 *     "get",
 *     "post",
 *     "count_posts"={
 *         "method"="GET",
 *         "path"="/posts/allposts",
 *         "controller"=PostCountController::class,
 *         "read" = false,"filters" = {},"pagination_enabled"=false,
 *         "openapi_context"={
 *              "summary"="Récupére le nombre d'article total",
 *              "parameters"={
 *                  {
 *              "in"="query", "name" ="online",
 *              "schema"={"type"="integer", "maximum"=1, "minimum"=0},
 *              "description"="Filtre des articles en ligne"}
 *              },
 *          "response"={
 *                  "200"={"description"="OK", "content"={"application/json"={"schema"={"type"="integer","example"=3}}}}
 *              },
 *          }
 *       }
 *     },
 *     itemOperations={
 *     "put",
 *     "delete",
 *     "get"={"normalization_context"={"groups"={"read:item", "read:collection", "read:Post"}},
 *     "openapi_definition_name"="Detail"},
 *     "publish"={
 *         "method"="POST",
 *         "path"="/posts/{id}/publication",
 *         "controller"=PostPublishController::class,
 *         "openapi_context"={
 *              "summary"="Permet de publier un article",
 *              "requestBody"={
 *                  "content"={"application/json"={"schema"={}}}
 *              }
 *          }
 *       },
 *     },
 *     paginationItemsPerPage=2,
 *     paginationMaximumItemsPerPage=2,
 *     paginationClientItemsPerPage=true
 *     )
 * @ApiFilter(
 *     SearchFilter::class, properties={"id": "exact", "title": "partial"}
 * )
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection","read:item", "write:item"})
     * @Assert\Length(min=5,groups={"create:collection"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection","read:item", "write:item"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read:item", "write:item"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:item"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts", cascade={"persist"})
     * @Groups({"read:item", "write:Post"})
     * @Assert\Valid()
     */
    private $category;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     * @Groups({"read:collection"})
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "type"="boolean",
     *             "description"="En ligne ou pas ?"
     *         }
     *     }
     * )
     */
    private $online = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
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

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
