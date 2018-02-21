<?php
namespace App\Entities;

use Illuminate\Http\Request;

class Actor
{
    private $firstname; 
    private $lastname; 
    private $country; 
    private $uuid; 

    public function __construct(
        $firstname,
        $lastname,
        $country,
        $uuid
    )
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->country = $country;
        $this->uuid = $uuid;
    }

    public static function importFrom(Request $request, UuidGenerator $generator)
    {
        return new self(
            $request->input('firstname'), 
            $request->input('lastname'),
            $request->input('country'),
            $generator->toString()
        );
    }      

	public function export()
	{
	    return [
            'uuid' => $this->uuid,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'country' => $this->country
        ];
	}
}
