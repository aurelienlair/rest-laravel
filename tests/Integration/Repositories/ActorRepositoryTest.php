<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Repositories\SqliteActorRepository;
use App\Repositories\SqlCriteria;
use App\Entities\UuidGenerator;
use App\Entities\Actor;
use App\Entities\RamseyUuidGenerator;
use Mockery;

class ActorRepositoryTest extends TestCase
{
    private $repository;
    private $uuid;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new SqliteActorRepository(
            $this->createApplication()->make('Illuminate\Database\ConnectionInterface')
        );
    }

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->uuid = Mockery::mock(UuidGenerator::class);
        $this->uuid->shouldReceive('toString')->andReturn('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a');
    }

    public function testNewActorPersistence()
    {
        $actor = $this->newActor([], $this->uuid);
$expectedQuery = <<<EOT
INSERT INTO actors
('uuid','first_name','last_name','country')
VALUES
('25769c6c-d34d-4bfe-ba98-e0ee856f3e7a','Michael','Douglas','US');
EOT;
        $db = $this->createMock(ConnectionInterface::class);
        $db->expects($this->once())
            ->method('insert')
            ->with($expectedQuery);
		$repository = new SqliteActorRepository($db);
        $repository->add($actor);
    }

    public function testFindANewActorInRepository()
    {
        $this->repository->add($this->newActor());

        $expectedActor = $this->newActor([
            'firstname' => 'Kirk',
            'lastname' => 'Douglas',
        ]);
        $this->repository->add($expectedActor);

        $criteria = SqlCriteria::from([
            'first_name' => 'Kirk',
            'last_name' => 'Douglas',
            'country' => 'US',
        ]);
        $dataSet = $this->repository->findBy($criteria);

        $this->assertEquals(1, $dataSet->count());
        $this->assertEquals($expectedActor->export(), $dataSet->current()->export());
    }

    public function testUpdateAnActorInRepository()
    {
        $actor = $this->newActor(['country' => 'GB']);
        $this->repository->add($actor);
        $actor = new Actor(
            $actor->export()['firstname'],
            $actor->export()['lastname'],
            'US',
            $actor->export()['uuid']
        );
        $this->repository->update($actor);
        $dataSet = $this->repository->findBy(
            SqlCriteria::from(['uuid' => $actor->export()['uuid']])
        );
        $this->assertEquals('Michael', $dataSet->current()->export()['firstname']);
        $this->assertEquals('Douglas', $dataSet->current()->export()['lastname']);
        $this->assertEquals('US', $dataSet->current()->export()['country']);
    }

    public function testFindAllActorsInRepository()
    {
        $this->repository->add($this->newActor());
        $this->repository->add(
            $this->newActor([
                'firstname' => 'Kirk',
                'lastname' => 'Douglas',
                'country' => 'US',
            ])
        );
        $this->repository->add(
            $this->newActor([
                'firstname' => 'Michelle',
                'lastname' => 'Pfeiffer',
                'country' => 'UK',
            ])
        );
        $this->assertEquals(3, $this->repository->findAll()->count());
    }

    public function testRemoveAnExistingActorFromRepository()
    {
        $actor = $this->newActor();
        $this->repository->add($actor);
        $this->repository->remove($actor);
        $criteria = SqlCriteria::from(['uuid' => $actor->export()['uuid']]);
        $dataSet = $this->repository->findBy($criteria);
        $this->assertEquals(0, $dataSet->count());
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    private function requestStub(array $parameters)
    {
        $request = Mockery::mock(Request::class);

        foreach ($parameters as $name => $value) {
            $request->shouldReceive('input')->with($name)->andReturn($value);
        }
        return $request;
    }

    private function newActor(array $personalData=[], $uuid=null)
    {
        $data = array_merge(
            [
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US',
            ],
            $personalData
        );

        if (is_null($uuid)) {
            $uuid = new RamseyUuidGenerator(4);
        }

        return Actor::importFrom(
            $this->requestStub($data),
            $uuid
        );
    }
}
