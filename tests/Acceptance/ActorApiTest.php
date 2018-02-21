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
        $response = $this->json(
            'POST',
            '/api/actors', 
            [
                'firstname' => 'Ewan',
                'lastname' => 'McGregor',
                'country' => 'GB'
            ]
        );

        $response->assertStatus(201);
		$this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertRegExp(
            '#http://localhost/api/actors/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}#',
            $response->headers->get('Location')
        );
    }  

    public function testShouldGetNewActorResource()
    {
        $response = $this->json(
            'POST',
            '/api/actors', 
            [
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );
        $this->json(
            'POST',
            '/api/actors', 
            [
                'firstname' => 'Kirk',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );

        $url = $response->headers->get('Location');
        $response->assertStatus(201);
        preg_match('#[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}#', $url, $uuid);
        $response = $this->json('GET', $url);
        $response->assertStatus(200);
		$this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(
            json_encode(
                [
                    'uuid' => $uuid[0],
                    'firstname' => 'Michael',
                    'lastname' => 'Douglas',
                    'country' => 'US'
                ],
                true
            ), 
            $response->getContent()
        );
    }

    public function testCantGetNewActorResourceWithAnInvalidUid()
    {
        $url ='http://localhost/api/actors/WRONG_ID';
        $response = $this->json('GET', $url);
        $response->assertStatus(404);
    }

    public function testShouldModifyAnExistingActor()
    {
        $response = $this->json(
            'POST',
            '/api/actors', 
            [
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'FR'
            ]
        );

        $response->assertStatus(201);
        $url = $response->headers->get('Location');
        preg_match('#[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}#', $url, $uuid);
        $response = $this->json(
            'PUT',
            $url, 
            [
                'uuid' => $uuid[0],
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );
        $response->assertStatus(200);
        $this->assertEquals(
            json_encode(
                [
                    'uuid' => $uuid[0],
                    'firstname' => 'Michael',
                    'lastname' => 'Douglas',
                    'country' => 'US'
                ],
                true
            ), 
            $response->getContent()
        );
    }

    public function testCannotUpdateActorWithWrongUid()
    {
        $wrongUuid = 'WRONG_ID';
        $url ='http://localhost/api/actors/' . $wrongUuid;
        $response = $this->json(
            'PUT', 
            $url,
            [
                'uuid' => $wrongUuid,
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );
        $response->assertStatus(404);
    }

    public function testCannotUpdateActorIfEntityDoesNotExist()
    {
        $unknownUuid = '00000000-0000-4000-a000-000000000000';
        $url ='http://localhost/api/actors/' . $unknownUuid;
        $response = $this->json(
            'PUT', 
            $url,
            [
                'uuid' => $unknownUuid,
                'firstname' => 'Michael',
                'lastname' => 'Douglas',
                'country' => 'US'
            ]
        );
        $response->assertStatus(422);
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }
}
