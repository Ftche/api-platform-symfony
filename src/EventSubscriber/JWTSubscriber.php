<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function onLexikJwtAuthenticationOnJwtDecoded( JWTCreatedEvent  $event)
    {
        $data = $event->getData();
        $data['id'] = $event->getUser()->getId();
        $data['email'] = $event->getUser()->getEmail();
        $data['username'] = $event->getUser()->getUsername();

        $event->setData($data);
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtDecoded',
        ]; // 'lexik_jwt_authentication.on_jwt_created'
    }
}
