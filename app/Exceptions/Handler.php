<?php

namespace App\Exceptions;

use App\Enums\ErrorCode;
use App\Logger\Formatters\BetterHtmlFormatter;
use App\Logger\Processors\ExtraDataProcessor;
use App\Providers\RouteServiceProvider;
use App\Traits\Http\ApiResponse;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Debug\Exception\FlattenException;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        MethodNotAllowedHttpException::class,
    ];

    protected $dontReportStatusCodes = [
        403, 404,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        if ($exception instanceof HttpException && in_array($exception->getStatusCode(), $this->dontReportStatusCodes)) {
            return;
        }

        if ($this->shouldReportToSentry($exception)) {
            app('sentry')->captureException($exception, [
                'extra' => SentryContext::capture(request()),
            ]);
        }

        parent::report($exception);
    }

    /**
     * Determines if given Exception should be reported to Sentry.
     *
     * @param Exception $exception
     *
     * @return bool
     */
    protected function shouldReportToSentry(Exception $exception): bool
    {
        $app = app();

        return \HApp::isProduction() && $app->bound('sentry') && $this->shouldReport($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if (RouteServiceProvider::isRESTApiRequest($request)) {
            return $this->RESTApiHandler($request, $exception);
        }

        $status = 500;
        \HMisc::debug(get_class($exception));
        \HMisc::debug($exception->getMessage());
        \HMisc::debug($exception->getTraceAsString());

        if (false !== strpos($request->path(), 'api/') || $request->wantsJson() || $request->isXmlHttpRequest()) {
            $response = [
                'type' => 'error',
                'data' => ['Sorry, something went wrong.'],
                'code' => ErrorCodes::UNKNOWN_ERROR,
            ];

            if ($exception instanceof HttpException) {
                $status = $exception->getStatusCode();
            }

            if ($exception instanceof ValidationConstraintException) {
                $status = 400;
                $response = [
                    'type' => 'error',
                    'data' => $exception->getConstraint(),
                    'code' => ErrorCodes::VALIDATION_ERROR,
                ];
                $normalException = true;
            }

            if ($exception instanceof ValidationException) {
                $status = 400;
                $messageBag = $exception->validator->getMessageBag();
                $response = [
                    'type' => 'error',
                    'data' => $messageBag->toArray(),
                    'code' => ErrorCodes::VALIDATION_ERROR,
                ];
                $normalException = true;
            }

            if ($exception instanceof AuthenticationException) {
                $status = 403;
                $response = [
                    'type' => 'error',
                    'data' => ['Authentication error'],
                    'code' => ErrorCodes::AUTHENTICATION_ERROR,
                ];
                $normalException = true;
            }

            if (config('app.debug')) {
                if (!isset($normalException)) {
                    http_response_code(500);
                    echo get_class($exception)."\n";
                    echo $exception->getMessage();
                    echo "\n\n";
                    echo $exception->getTraceAsString();

                    die;
                }

                $response['debug'] = [
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ];
            }

            return response()->json($response, $status, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->view('errors.404');
        }

        if (!config('app.debug')) {
            $regularException = $exception instanceof ValidationException || $exception instanceof AuthenticationException;
            // hide exception details before render

            if ($this->isHttpException($exception)) {
                /* @var HttpException $exception */
                // replace exception instance with empty one when debug is off
                $exception = new HttpException($exception->getStatusCode());
            } elseif (!$regularException) {
                $exception = new HttpException(500);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (false !== strpos($request->path(), 'api/') || $request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json(['type' => 'error', 'data' => ['Unauthenticated.'], 'code' => ErrorCodes::UNAUTHENTICATED], 401);
        }

        return redirect()->guest(route('admin.login'));
    }

    /**
     * Create a Symfony response for the given exception.
     *
     * @param \Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        $betterHtmlFormatter = new BetterHtmlFormatter('d.m.y H:i:s');

        $timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        $datetime = new \DateTime(null, $timezone);
        $record = [
            'message' => (string) $e,
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'local',
            'datetime' => $datetime,
            'extra' => [],
            'context' => ['exception' => $e],
        ];

        $e = FlattenException::create($e);

        if (config('app.debug')) {
            $record = call_user_func(new ExtraDataProcessor(), $record);
            $html = $betterHtmlFormatter->format($record);
        } else {
            $handler = new SymfonyExceptionHandler(false);
            $html = $handler->getHtml($e);
        }

        return SymfonyResponse::create($html, $e->getStatusCode(), $e->getHeaders());
    }

    private function getErrorsFromValidator(Validator $validator): array
    {
        $errors = [];

        foreach ($validator->getMessageBag()->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }

        return $errors;
    }

    private function RESTApiHandler(Request $request, \Exception $exception)
    {
        if (env('DISPLAY_JSON_API_ERRORS', false)) {
            dd($exception);
        }

        if ($exception instanceof HttpException) {
            return $this->responseError(
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getCode()
            );
        }

        if ($exception instanceof ValidationException) {
            return $this->responseError(
                Response::HTTP_BAD_REQUEST,
                $exception->getMessage(),
                ErrorCode::VALIDATION_ERROR,
                $this->getErrorsFromValidator($exception->validator)
            );
        }

        if ($exception instanceof ValidationConstraintException) {
            return $this->responseError(
                Response::HTTP_BAD_REQUEST,
                $exception->getMessage(),
                ErrorCode::VALIDATION_ERROR,
                [$exception->getConstraint()]
            );
        }

        if (!\HApp::isProduction()) {
            return $this->serverError($exception->getMessage());
        }

        return $this->serverError();
    }
}
