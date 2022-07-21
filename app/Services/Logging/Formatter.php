<?php

namespace App\Services\Logging;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Formatter
{
    const MAX_LENGTH = 2048;

    /**
     * @param Request|mixed $request
     * @param Response|JsonResponse|mixed $response
     * @param float $end_timestamp
     *
     * @return string
     */
    public function format($request, $response, float $end_timestamp): string
    {
        return <<<TXT

Method: {$request->method()}
URL: {$request->fullUrl()}
Input: {$request->getContent()}
User ID: {$this->getUserId()}
IP: {$request->ip()}
Duration: {$this->getDuration($end_timestamp)}
Code: {$response->getStatusCode()}
Output: {$this->getOutput($response)}

TXT;
    }

    /**
     * @return mixed
     */
    private function getUserId()
    {
        $current_user = Auth::user();

        return $current_user ? $current_user->getAuthIdentifier() : 'Unknown';
    }

    /**
     * @param float $end_timestamp
     *
     * @return string
     */
    private function getDuration(float $end_timestamp): string
    {
        if (defined('LARAVEL_START')) {
            return number_format($end_timestamp - LARAVEL_START, 3);
        }

        if (defined('LUMEN_START')) {
            return number_format($end_timestamp - LUMEN_START, 3);
        }

        return 'Unknown';
    }

    /**
     * @param Response|JsonResponse|mixed $response
     *
     * @return string
     */
    private function getOutput($response): string
    {
        if ($response instanceof JsonResponse) {
            $content = $response->getContent();
            $result = strlen($content) > Formatter::MAX_LENGTH
                ? mb_substr($content, 0, Formatter::MAX_LENGTH, "UTF-8") . '...'
                : $content;
        } else {
            $result = 'Not JSON Response';
        }

        return $result;
    }
}
