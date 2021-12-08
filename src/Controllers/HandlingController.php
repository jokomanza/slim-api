<?php

namespace App\Controllers;

use JSend\JSendResponse;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class HandlingController {

    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Get Student List
    public function getArea(Request $request, Response $response, $args)
    {
        $areas = [
            [
                'nama' => 'ICT'
            ],
            [
                'nama' => 'Akuntansi'
            ]
        ];

        return $response->withJson(JSendResponse::success($areas));
    }
}