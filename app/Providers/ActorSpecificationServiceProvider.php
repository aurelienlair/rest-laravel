<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Exceptions\InvalidActorSpecification;
use Exception;

class ActorSpecificationServiceProvider extends ServiceProvider
{
    private $rules;
    private $request;

    use ValidatesRequests {
        validate as check;    
    }

    public static function from(Request $request, array $rules)
    {
        return new self($request, $rules);
    }

    public function __construct(Request $request, array $rules)
    {
        $this->request = $request;
        $this->rules = $rules;
    }

    public function validate()
    {
        try {
            $this->check($this->request, $this->rules);
        } catch (Exception $e) {
            throw new InvalidActorSpecification();
        }
    }
}
