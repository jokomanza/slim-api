<?php

namespace App\Controllers;

use alsvanzelf\jsonapi\CollectionDocument;
use alsvanzelf\jsonapi\ErrorsDocument;
use alsvanzelf\jsonapi\objects\ErrorObject;
use alsvanzelf\jsonapi\objects\ResourceObject;
use alsvanzelf\jsonapi\ResourceDocument;
use App\Models\Actor;
use App\Models\Film;
use Exception;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\QueryException;
use JSend\JSendResponse;
use PDO;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class FilmController
{

    protected $container;
    protected $router;
    protected $db;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->db = $container->get('db');
    }

    // Get Student List
    public function getCountry(Request $request, Response $response, $args)
    {
        $areas = [
            [
                'nama' => 'ICT'
            ],
            [
                'nama' => 'Akuntansi'
            ]
        ];

        $page = $request->getQueryParam('page', 1);
        $limit = $request->getQueryParam('limit', 5);

        $error = new ErrorsDocument();
        if (!is_numeric($page)) {
            $error->add(400, 'page invalid', 'query param page harus berupa angka');
        }
        if (!is_numeric($limit)) {
            $error->add(400, 'limit invalid', 'query param limit harus berupa angka');
        }
        if (count($error->toArray()) > 2) {
            return $response->withJson($error, 400);
        }

        $db = $this->container->get('db');
        $target_page = ($page > 1) ? ($page * $limit) - $limit : 0;

        $row_count = $db->query('SELECT COUNT(*) total FROM country')->fetch(PDO::FETCH_OBJ)->total;

        $total_page = ceil($row_count / $limit);


        // if ($target_page > $total_page) {
        //     $document = new ErrorsDocument();
        //     $document->add(400, 'Data does not exist');
        //     return $response->withJson($document);
        // }


        $previous = $target_page - 1 >= 0 ? $page - 1 : null;
        $next = $target_page + 1 <= $total_page ? ($page + 1) : null;
        $first = 1;
        $last = $total_page;
        $url = (string) $request->getUri()->withQuery('');

        $stmt = $db->query("SELECT * FROM country LIMIT $limit OFFSET $target_page");

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $document = new CollectionDocument();

        foreach ($result as $item) {
            // var_dump($item);
            // die;
            $county = new ResourceDocument('country', $item->country_id);
            $county->add('name', $item->country);
            $county->add('last_update', $item->last_update);

            $document->addResource($county);
        }

        $execution_time = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000;
        $document->addMeta('processing_time', "$execution_time milliseconds");
        $document->addMeta('processing_time_ms', $execution_time);
        $document->addMeta('total_records', $row_count);
        $document->addMeta('page', $page);
        $document->addMeta('limit', $limit);
        $document->addMeta('count', count($result));
        $document->setSelfLink((string) $request->getUri()->withQuery(''));
        $document->setPaginationLinks($previous = is_null($previous) ? null : "$url?page=$previous&limit=$limit", $next = is_null($next) ? null : "$url?page=$next&limit=$limit", $first = is_null($first) ? null : "$url?page=$first&limit=$limit", $last = is_null($last) ? null : "$url?page=$last&limit=$limit");
        $document->unsetJsonapiObject();



        return $response->withJson($document);
    }


    // Get Student List
    public function getFilm(Request $request, Response $response, $args)
    {
        $page = $request->getQueryParam('page', 1);
        $limit = $request->getQueryParam('limit', 5);

        $error = new ErrorsDocument();

        if (!is_numeric($page)) {
            $error->add(400, 'page invalid', 'query param page harus berupa angka');
        }
        if (!is_numeric($limit)) {
            $error->add(400, 'limit invalid', 'query param limit harus berupa angka');
        }
        if (count($error->toArray()) > 2) {
            return $response->withJson($error, 400);
        }

        $page = (int) $page;
        $limit = (int) $limit;

        $db = $this->container->get('db');
        $target_page = ($page > 1) ? ($page * $limit) - $limit : 0;

        $row_count = $db->query('SELECT COUNT(*) total FROM film')->fetch(PDO::FETCH_OBJ)->total;

        $total_page = ceil($row_count / $limit);

        $previous = $target_page - 1 >= 0 ? $page - 1 : null;
        $next = $target_page + 1 <= $total_page ? ($page + 1) : null;
        $first = 1;
        $last = $total_page;
        $url = (string) $request->getUri()->withQuery('');

        $stmt = $db->query("SELECT * FROM film ORDER BY film_id LIMIT $limit OFFSET $target_page");

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $document = new CollectionDocument();

        foreach ($result as $item) {
            // var_dump($item);
            // die;
            $film = new ResourceDocument('film', $item->film_id);
            foreach ($item as $key => $data) {
                $film->add($key, $data);
            }

            $film_id = $item->film_id;
            $actors = $db->query("SELECT a.actor_id, a.first_name, a.last_name FROM film_actor fa, actor a WHERE fa.actor_id = a.actor_id AND fa.film_id = $film_id ORDER BY film_id")->fetchAll(PDO::FETCH_OBJ);

            // var_dump($actors);
            // die;
            $actors_data = [];
            foreach ($actors as $actor) {
                $data = ResourceDocument::fromObject($actor, 'actor', $actor->actor_id);
                $actor_url = $this->container->get('router')->pathFor('actor_detail', ['actor_id' => $actor->actor_id]);
                // dump($request->getUri()->withPath('')->withQuery('')->withFragment('') . $actor_url);
                // die;
                $data->setSelfLink($actor_url);
                $actors_data[] = $data;
            }
            $actorsRelationalshipLinks = [
                # 'self' => null,
                'related'    => $this->router->relativePathFor('actors_by_film', ['film_id' => $film_id]),
            ];
            $film->addRelationship('actors', $actors_data, $actorsRelationalshipLinks);

            $document->addResource($film);
        }

        $execution_time = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000;
        $document->addMeta('processing_time', "$execution_time milliseconds");
        $document->addMeta('processing_time_ms', $execution_time);
        $document->addMeta('total_records', $row_count);
        $document->addMeta('page', $page);
        $document->addMeta('limit', $limit);
        $document->addMeta('count', count($result));
        $document->setSelfLink((string) $request->getUri()->withQuery(''));
        $document->setPaginationLinks($previous = is_null($previous) ? null : "$url?page=$previous&limit=$limit", $next = is_null($next) ? null : "$url?page=$next&limit=$limit", $first = is_null($first) ? null : "$url?page=$first&limit=$limit", $last = is_null($last) ? null : "$url?page=$last&limit=$limit");
        $document->unsetJsonapiObject();



        return $response->withJson($document);
    }



    // Get Student List
    public function getActorsByFilm(Request $request, Response $response, $args)
    {

        $film_id = (int) $args['film_id'];

        $db = $this->container->get('db');

        $url = (string) $request->getUri()->withQuery('');

        $actors = $db->query("SELECT a.actor_id, a.first_name, a.last_name, a.last_update FROM film_actor fa, actor a WHERE fa.actor_id = a.actor_id AND fa.film_id = $film_id ORDER BY film_id")->fetchAll(PDO::FETCH_OBJ);

        $document = new CollectionDocument();

        foreach ($actors as $item) {
            // var_dump($item);
            // die;
            $county = new ResourceDocument('actor', $item->actor_id);
            foreach ($item as $key => $data) {
                $county->add($key, $data);
            }
            $document->addResource($county);
        }

        $execution_time = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000;
        $document->addMeta('processing_time', "$execution_time milliseconds");
        $document->addMeta('processing_time_ms', $execution_time);
        $document->setSelfLink((string) $request->getUri()->withQuery(''));
        $document->unsetJsonapiObject();

        return $response->withJson($document);
    }

    /**
     * Get actor detail
     * 
     * @return Request
     */
    public function getActorDetail(Request $request, Response $response, $args)
    {
        $this->db->beginTransaction();
        // $this->db->commit();
        $this->db->rollBack();

        $actors = (new Actor)->get($args['actor_id']);

        $document = createCollectionDocument($request);
        foreach ($actors as $item) {
            $county = new ResourceDocument('actor', $item->actor_id);
            foreach ($item as $key => $data) {
                $county->add($key, $data);
            }
            $document->addResource($county);
        }

        $document->addMeta('message', 'Success getting actor detail');

        return $response->withJson($document);
    }
}
