<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\ConnectionInterface;
use App\Providers\ActorSpecificationServiceProvider as Specification;
use App\Repositories\SqliteActorRepository;
use App\Entities\Actor;
use App\Entities\RamseyUuidGenerator;
use App\Repositories\SqlCriteria;
use App\Exceptions\UnprocessableActor;

class ActorController extends BaseController
{
    private $repository;

    public function __construct(ConnectionInterface $connection)
    {
        $this->repository = new SqliteActorRepository($connection);
    }

    public function store(Request $request)
    {
        $this->actorValidationFrom($request);

        $this->repository->add(
            Actor::importFrom(
                $request,
                new RamseyUuidGenerator(4)
            )
        );

        $newActor = $this->findActorFrom(
            SqlCriteria::from([
                'first_name' => $request->input('firstname'),
                'last_name' => $request->input('lastname'),
                'country' => $request->input('country')
            ])
        );
        
        $resourceUrl = $request->url() . '/' . $newActor->export()['uuid'];

        return response()
            ->json([])
            ->setStatusCode(201)
            ->header('Content-Type', 'application/json')
            ->header('Location', $resourceUrl);
    }    

    public function show($uuid)
    {
        $actor = $this->findActorFrom(
            SqlCriteria::from([
                'uuid' => $uuid
            ])
        );

        return response()
            ->json($actor->export())
            ->setStatusCode(200)
            ->header('Content-Type', 'application/json');
	}

    public function update(Request $request, $uuid)
    {
        $this->actorValidationFrom($request);

        $actor = new Actor(
            $request->input('firstname'),
            $request->input('lastname'),
            $request->input('country'),
            $uuid
        );

        $this->repository->update($actor);

        $actor = $this->findActorFrom(
            SqlCriteria::from([
                'uuid' => $uuid
            ])
        );

        if (is_null($actor)) {
            throw new UnprocessableActor(); 
        }

        return response()
            ->json($actor->export())
            ->setStatusCode(200)
            ->header('Content-Type', 'application/json');
	}

    public function remove($uuid)
    {
        $actor = $this->findActorFrom(
            SqlCriteria::from([
                'uuid' => $uuid
            ])
        );

        if (is_null($actor)) {
            throw new UnprocessableActor(); 
        }

        $this->repository->remove($actor);

        return response()
            ->json()
            ->setStatusCode(202)
            ->header('Content-Type', 'application/json');
	}

    private function actorValidationFrom(Request $request)
    {
        Specification::from(
            $request,
            [
                'firstname' => 'required|string|max:40',
                'lastname' => 'required|string|max:40',
                'country' => 'required|string|max:2',
            ]
        )->validate();
    }

    private function findActorFrom(SqlCriteria $criteria)
    {
        return $this->repository->findBy($criteria)->current();
    }
}
