<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Attributes\ApiAuthGroups;
use App\Controller\EmptyController;
use App\Repository\PostRepository;
use App\Controller\PostPublishController;
use App\Controller\PostCountController;
use App\Controller\PostImageController;
use App\Resolver\PostMaxIdResolver;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ]
        ],
        'post',
        'count' => [
            'method' => 'GET',
            'path' => '/posts/allcounts',
            'controller' => PostCountController::class,
            'read' => false,
            'filters' => [],
            'pagination_enabled' => false,
            'openapi_context' => [
                'summary' => 'Recupere le nombre total de nos articles',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0
                        ],
                        'description' => 'Filtre les articles en ligne'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    graphql: [
        'item_query',
        'collection_query' => [
            'pagination_type' => 'page'
        ],
        'create' => [
            'validation_groups' => ['create:Post'],
            'security' => 'is_granted("ROLE_USER")'
        ],
        'update',
        'delete',
        'maxIdQuery' => [
            'collection_query' => PostMaxIdResolver::class,
            'read' => false,
            'pagination_enabled' => false,
            'args' => [
                'id' => ['type' => 'Int!'],
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection', 'read:item', 'read:Post'],
                'openapi_definition_name' => 'Detail'
            ]
        ],
        'put',
        'delete',
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostPublishController::class,
            'openapi_context' => [
                'summary' => 'Permet de publier un article',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => []
                        ]
                    ]
                ]
            ]
        ],
        'image' => [
            'method' => 'POST',
            'path' => '/posts/{id}/image',
            'controller' => EmptyController::class,
            'validation_groups' => ['write:Post'],
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:Post']],
    normalizationContext: ['groups' => ['read:collection'], 'openapi_definition_name' => 'Collection'],
    paginationClientItemsPerPage: true,
    # paginationItemsPerPage: 2,
    # paginationMaximumItemsPerPage: 2
),
ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial']),
ApiFilter(OrderFilter::class, properties: ['id', 'title']),
ApiAuthGroups([
    'CAN_EDIT' => ['read:colleection:Owner'],
    'ROLE_USER' => ['read:collection:User']
    ])
]
class Post implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 5, groups: ['create:Post'])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection:User', 'write:Post'])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:collection', 'write:Post'])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:item'])]
    private $createdAd;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts', cascade: ['persist'])]
    #[
        Groups(['read:item', 'write:Post']),
        Valid()
    ]
    private $category;

    #[ORM\Column(type: 'boolean', options: ["default"=>0])]
    #[
        Groups(['read:collection:User']),
        ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'En ligne ou pas ?'])
    ]
    private $online = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $filePath;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_image", fileNameProperty="filePath")
     */
    #[Groups(['write:Post'])]
    private ?File $file = null;

    /**
     * @var string|null
     */
    #[Groups(['read:collection'])]
    private $fileUrl;

    /**
     * Post constructor.
     */
    public function __construct( )
    {
        $this->createdAd = new \DateTime();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function validationGroups(self $post) {
        return ['create:Post'];
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

    public function getCreatedAd(): ?\DateTimeInterface
    {
        return $this->createdAd;
    }

    public function setCreatedAd(\DateTimeInterface $createdAd): self
    {
        $this->createdAd = $createdAd;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
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


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * return Post
     */
    public function setFile(?File $file): Post
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * @param string|null $fileUrl
     */
    public function setFileUrl(?string $fileUrl): Post
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }



}
