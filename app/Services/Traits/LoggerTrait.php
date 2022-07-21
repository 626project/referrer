<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Log;

trait LoggerTrait
{
    /**
     * @param $text
     * @param array $params
     */
    private function log($text, array $params = [])
    {
        Log::info('[' . ($this->logger_label ?? 'logger:trait') . ']: ' . vsprintf($text, $params));
    }

    /**
     * @param $text
     * @param array $params
     */
    private function debug($text, array $params = [])
    {
        Log::debug('[' . ($this->logger_label ?? 'logger:trait') . ']: ' . vsprintf($text, $params));
    }
}
