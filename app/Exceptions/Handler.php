<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        error_log('CLASS: ' . get_class($exception));
        error_log('EXCEPTION:' . PHP_EOL . $exception->getMessage());
        error_log('TRACE:' . PHP_EOL . $exception->getTraceAsString());

        if ($request->is('api/*')) {
            switch (get_class($exception)) {
                case 'App\Exceptions\InvalidActorSpecification':
                    return response()->json('Wrong actor specification')->setStatusCode(400);
                case 'App\Exceptions\UnprocessableActor':
                    return response()->json('Unable to process the requested actor')->setStatusCode(422);
                case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                    return response()->json('Resource does not exist')->setStatusCode(404);
                default:
                    return response()->json('An error has occurred')->setStatusCode(500);
            }
        }

        return parent::render($request, $exception);
    }
}
