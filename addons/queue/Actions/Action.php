<?php

namespace BoldMinded\Queue\Actions;

use ExpressionEngine\Service\Addon\Controllers\Action\AbstractRoute;

abstract class Action extends AbstractRoute
{
    protected function sendJsonResponse(string|array $data): void
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        ee()->output->enable_profiler(false);
        @header('Content-Type: text/html; charset=UTF-8');
        exit($data);
    }
}
