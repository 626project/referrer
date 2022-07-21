<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    private const MESSAGE_METHOD_NOT_ALLOWED = 'Method Not Allowed';
    private const MESSAGE_INTERNAL_SERVER_ERROR = 'Internal Server Error';

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
    public function report(Exception $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return JsonResponse|Response
     */
    public function render($request, Exception $exception)
    {
        if (!config('app.debug')) {
            if (method_exists($request, 'route')) {
                $route = $request->route();
                if (method_exists($route, 'middleware')) {
                    $middleware = $route->middleware();
                    if (!is_array($middleware)) {
                        $middleware = [];
                    }
                    if (in_array('api', $middleware, true)) {
                        return $this->renderApi($exception);
                    }
                }
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Exception $exception
     *
     * @return JsonResponse
     */
    public function renderApi(Exception $exception): JsonResponse
    {
        $errors = null;
        $code = null;

        switch (true) {
            case $exception instanceof MethodNotAllowedHttpException:
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
                $exception = new MethodNotAllowedHttpException([], self::MESSAGE_METHOD_NOT_ALLOWED, $exception);
                break;

            case $exception instanceof ValidationException:
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $errors = $exception->errors();
                break;

            case $exception instanceof NotFoundHttpException:
            case $exception instanceof ModelNotFoundException:
                $status = Response::HTTP_NOT_FOUND;
                break;

            case $exception instanceof AuthorizationException:
                $status = Response::HTTP_FORBIDDEN;
                break;

            case $exception instanceof HttpException:
                $status = $exception->getStatusCode();
                break;

            default:
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
                $exception = new HttpException($status, self::MESSAGE_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'success' => false,
            'message' => $exception->getMessage()
        ];

        if ($code) {
            $data['code'] = $code;
        }

        if ($errors) {
            $data['errors'] = $errors;
        }

        return response()->json($data, $status);
    }
}
