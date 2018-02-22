<?php
namespace Tests\Acceptance;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ActorApiTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function testShouldCreatedANewActor()
    {
        $response = $this->actorApiRequest('POST')->assertStatus(201);
        $this->assertEquals(
            'application/json',
            $response->headers->get('Content-Type')
        );
        $this->assertRegExp(
            '#http://localhost/api/actors/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}#',
            $response->headers->get('Location')
        );
    }  

    public function testShouldGetNewActorResource()
    {
        $this->actorApiRequest('POST');
        $response = $this->actorApiRequest(
            'POST',
            [
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );
        $url = $response->headers->get('Location');
        $response->assertStatus(201);
        $response = $this->json('GET', $url)->assertStatus(200);
		$this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(
            json_encode(
                [
                    'uuid' => $this->uuidFromUrl($url),
                    'firstname' => 'Michael',
                    'lastname' => 'Douglas',
                    'country' => 'US'
                ],
                true
            ), 
            $response->getContent()
        );
    }

    public function testCantGetNewActorResourceWithAnInvalidUuid()
    {
        $url ='http://localhost/api/actors/WRONG_ID';
        $response = $this->json('GET', $url);
        $response->assertStatus(404);
    }

    public function testShouldModifyAnExistingActor()
    {
        $response = $this->actorApiRequest('POST')->assertStatus(201);
        $url = $response->headers->get('Location');
        $actorNewData = [
            'uuid' => $this->uuidFromUrl($url),
            'firstname' => 'Ewan',
            'lastname' => 'MacGregor',
            'country' => 'US'
        ];
        $response = $this->actorApiRequest('PUT', $actorNewData, $url)->assertStatus(200);
        $this->assertEquals(
            json_encode($actorNewData, true), 
            $response->getContent()
        );
    }

    public function testCannotUpdateActorWithWrongUid()
    {
        $wrongUuid = 'WRONG_ID';
        $url ='http://localhost/api/actors/' . $wrongUuid;
        $this->actorApiRequest(
            'PUT',
            [
                'uuid' => $wrongUuid,
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ],
            $url
        )->assertStatus(404);
    }

    public function testCannotUpdateActorIfEntityDoesNotExist()
    {
        $unknownUuid = '00000000-0000-4000-a000-000000000000';
        $url ='http://localhost/api/actors/' . $unknownUuid;
        $this->actorApiRequest(
            'PUT',
            [
                'uuid' => $unknownUuid,
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ],
            $url
        )->assertStatus(422);
    }

    public function testShouldRemoveAnExistingActor()
    {
        $response = $this->actorApiRequest('POST');
        $url = $response->headers->get('Location');
        $this->json('DELETE', $url)->assertStatus(202);
    }

    private function actorApiRequest($type, array $parameters=[], $url=null)
    {
        $data = array_merge(
            [
                'firstname' => 'Ewan',
                'lastname' => 'McGregor',
                'country' => 'GB'
            ],
            $parameters
        );

        if (empty($url)) {
            $url = '/api/actors';
        }

        return $this->json(
            $type,
            $url,
            $data
        );
    }

    private function uuidFromUrl($url)
    {
        preg_match('#[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}#', $url, $matches);

        return $matches[0];
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }
}
