<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /**
         * @var  PathItem $path
         */
        foreach ($openApi->getPaths()->getPaths() as $eky => $path){
            if ($path->getGet() && $path->getGet()->getSummary() === 'Hidden'){
                $openApi->getPaths()->addPath($eky, $path->withGet(null));
            }
        }
        $openApi->getPaths()->addPath('/ping', new PathItem(null, 'Ping',null,
            new Operation('ping-id',[],[],'Repond')));

        return $openApi;
    }
}