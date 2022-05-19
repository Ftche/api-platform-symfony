<?php

namespace App\Eventlistener;

use ApiPlatform\Core\EventListener\DeserializeListener as DecoratedListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializeListener
{
    public function __construct(
        private DecoratedListener $decorated,
        private SerializerContextBuilderInterface $serializerContextBuilder,
        private DenormalizerInterface $denormalizer
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)){
            return;
        }
//dd($request->getContentType());
        if($request->getContentType() == 'form' || $request->getContentType() == 'multipart'  ) {
            $this->denormalizeFromRequest($request);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeFromRequest(Request $request): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        //dd($attributes);
        if(empty($attributes)){
            return;
        }
        $context = $this->serializerContextBuilder->createFromRequest($request,false, $attributes);
        $populated = $request->attributes->get('data');
        if($populated != null){
            $context['object_to_populate'] = $populated;
        }
        $data = $request->request->all();
        $files = $request->files->all();
        $object = $this->denormalizer->denormalize(
            array_merge($data, $files),$attributes['resource_class'],null,$context
        );
        $request->attributes->set(
            'data',
            $this->denormalizer->denormalize(
                $data, $attributes['resource_class'], null, $context
            )
        );
        $request->attributes->set('data', $object);
    }
}