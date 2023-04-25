<?php namespace Jakten\Exceptions;

use Auth;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Jakten\Services\KKJTelegramBotService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class Handler
 * @package Jakten\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     *
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        Log::error($exception->getMessage());
        Log::error($exception);

        /** @var KKJTelegramBotService $kkjBot */
        $kkjBot = resolve(KKJTelegramBotService::class);
        $kkjBot->log('exception_halt', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        Log::info($exception->getMessage());

        if ($this->isApiRequest($request)) {

            // this part is from render function in Illuminate\Foundation\Exceptions\Handler.php
            // works well for json
            $exception = $this->prepareException($exception);

            if ($exception instanceof HttpResponseException) {
                return $exception->getResponse();
            } elseif ($exception instanceof AuthenticationException) {
                return $this->unauthenticated($request, $exception);
            } elseif ($exception instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($exception, $request);
            }

            // we prepare custom response for other situation such as modelnotfound
            $response = [];
            $response['error'] = $exception->getMessage();

            if (config('app.debug')) {
                $response['trace'] = $exception->getTrace();
                $response['code'] = $exception->getCode();
            }

            // we look for assigned status code if there isn't we assign 500/404
            $statusCode = method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500;

            $response['code'] = $statusCode;

            return response()->json($response, $statusCode);
        }


        if ($exception instanceof AccessDeniedHttpException) {

            switch (Auth::user()->getRoleName()) {
                case 'student':
                    return redirect()->route('student::dashboard');
                case 'organization':
                    return redirect()->route('organization::dashboard');
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isApiRequest($request)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Checks if this is a json api request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     **/
    private function isApiRequest($request)
    {
        return $request->expectsJson() || $request->is('api/*') || $request->is('v1/*');
    }
}
