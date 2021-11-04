<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     paginationEnabled=false,
 *     collectionOperations={"get","post"},
 *     itemOperations={
 *      "get",
 *     "delete",
 *      "put"={
 *             "denormalization_context"={"groups"={"put"}}
 *         }
 *     }
 * )
 */
class Dependency
{
    /**
     * @ApiProperty(
     *     identifier= true
     * )
     */
    private string $uuid;

    /**
     * @ApiProperty(
     *     description= "Nom de la dependance"
     * ),
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ApiProperty(
     *     description= "version de la dependance",
     * ),
     * @Assert\NotBlank()
     * @Groups("put")
     */
    private string $version;

    /**
     * @param string $name
     * @param string $version
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