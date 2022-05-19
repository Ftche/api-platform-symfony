<?php

namespace App\Serializer;

use App\Attributes\ApiAuthGroups;
use App\Entity\Post;
use App\Security\Voter\UserOwnedVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiAuthNormalizer implements ContextAwareNormalizerInterface,
    NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED_NORMALIZER = 'PostApiNormalizerAlreadyCalled';

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if(!is_object($data)){
            return false;
        }
        $class = new \ReflectionClass(get_class($data));
        $classAttributes = $class->getAttributes(ApiAuthGroups::class);
        $alreadyCalled = $context[self::ALREADY_CALLED_NORMALIZER] ?? false;
        return  $alreadyCalled === false && !empty($classAttributes);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $class = new \ReflectionClass(get_class($object));
        $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();
        foreach ($apiAuthGroups->groups as $role => $groups) {
            if($this->authorizationChecker->isGranted($role, $object)) {
                $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            }
        }
        $context[self::ALREADY_CALLED_NORMALIZER] = true;
        /*if ($this->authorizationChecker->isGranted(UserOwnedVoter::EDIT, $object)
            && isset($context['groups'])) {
            $context['groups'][] = 'read:collection:User';
        }*/
        return $this->normalizer->normalize($object, $format, $context);
    }

}