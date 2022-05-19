<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get',
        'put' => [
            'denormalization_context' => [
                'groups' => ['put:Dependency']
            ]
        ],
        'delete'
    ],
    paginationEnabled: false
)]
class Dependency
{
    #[ApiProperty(
        identifier: true
    )]
    private string $uuid;

    #[ApiProperty(
        description: 'Nom de la dépendance'
    ),
    Assert\Length(min: 2),
    Assert\NotBlank()]
    private string $name;

    #[ApiProperty(
        description: 'Version de la dépendance',
        openapiContext : [
            'example' => "0.0.*"
        ]
    ),
        Assert\Length(min: 2),
        Assert\NotBlank(),
        Groups(['put:Dependency'])
    ]
    private string $version;
// @param string $version

    /**
     * @param string $uuid
     * @param string $name
     */
    public function __construct(string $name, string $version)
    {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }


}