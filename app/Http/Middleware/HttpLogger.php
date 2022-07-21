<?php

namespace App\Http\Middleware;

use App\Services\Logging\Formatter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class HttpLogger
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * HttpLogger constructor.
     *
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function terminate($request, $response): void
    {
        $message = $this->formatter->format($request, $response, microtime(true));
        Log::channel('http')->info($message);
    }
}
