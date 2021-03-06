<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\ActorController;
use App\Providers\ActorSpecificationServiceProvider as Specification;

class ActorStorageRequestValidatorTest extends TestCase
{
    private $request;

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException App\Exceptions\InvalidActorSpecification
     */
    public function testActorSpecificationValidation()
    {
        $request = Request::create(
            'http://www.restisthebest.com',
            'POST',
            [
                'firstname' => 'Al', 
                'country' => 'US', 
            ]
        );
        
        Specification::from(
            $request,
            [
                'lastname' => 'required',
            ]
        )->validate();
    }
}
