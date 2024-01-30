<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \League\OAuth2\Server\Exception\OAuthServerException::class
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
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {

        if ($exception instanceof \League\OAuth2\Server\Exception\OAuthServerException && $exception->getCode() == 9) {
        }else if(app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (!($exception instanceof HttpException || $exception instanceof AuthenticationException)) {
            if (!config('app.debug')) {
                return response()->json(['message' => 'Something went wrong. Please contact system administrator'], 500);
            }
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(
            [
                'errors' => [
                    'status' => 401,
                    'message' => 'Unauthenticated',
                ]
            ], 401
        );
    }

    // public function render($request, Exception $e)
    // {
    //     $error = $this->convertExceptionToResponse($e);
    //     $response = [];
    //     if ($error->getStatusCode() == 500) {
    //         $response['error'] = $e->getMessage();
    //         if (env('APP_DEBUG', true)) {
    //             $response['trace'] = $e->getTraceAsString();
    //             $response['code'] = $e->getCode();
    //         }
    //     }
    //     return response()->json($response, $error->getStatusCode());
    // }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    /*protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest('login');
    }*/

    /* public function render($request, Exception $exception)
     {
         if ($exception instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
             return response()->json(['error' => 'Token has expired'], $exception->getStatusCode());
         } elseif ($exception instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
             return response()->json(['error' => 'Token is invalid'], $exception->getStatusCode());
         } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {
             return response()->json(['error' => 'Unauthorized'], 401);
         } elseif ($exception instanceof WebsiteTokenMissingException) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }

         return parent::render($request, $exception);
     }*/
}
