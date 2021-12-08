<?php

namespace App\Controllers;

use alsvanzelf\jsonapi\ResourceDocument;
use JSend\JSendResponse;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class MainController
{

    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Get Student List
    public function __invoke(Request $request, Response $response, $args)
    {
        $document = new ResourceDocument($type = 'RESTful API');
        $document->add('name', 'slim-api');

        $routes = $this->container->router->getRoutes();
        $routes = array_reduce($this->container->get('router')->getRoutes(), function ($target, $route) {
            $target[$route->getPattern()] = [
                'methods' => json_encode($route->getMethods()),
                # 'callable' => $route->getCallable(),
                # 'middlewares' => json_encode($route->getMiddleware()),
                'pattern' => $route->getPattern(),
                'name' => $route->getName(),
                'identifier' => $route->getIdentifier()
            ];
            return $target;
        }, []);

        $document->add('endpoints', $routes);
        $document->addMeta('copyright', 'Copyright 2021 Example Corp.');
        $document->addMeta('autors', [
            'Joko Supriyanto'
        ]);
        $document->setSelfLink((string) $request->getUri());


        return $response->withJson($document);
    }
}
