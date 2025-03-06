<?php

namespace BoldMinded\Queue\Queue\Exceptions;

use BoldMinded\Queue\Dependency\Illuminate\Contracts\Debug\ExceptionHandler;

class QueueException implements ExceptionHandler
{

    public function report(\Throwable $e)
    {
        // TODO: Implement report() method.
    }

    public function shouldReport(\Throwable $e)
    {
        // TODO: Implement shouldReport() method.
    }

    public function render($request, \Throwable $e)
    {
        // TODO: Implement render() method.
    }

    public function renderForConsole($output, \Throwable $e)
    {
        // TODO: Implement renderForConsole() method.
    }
}
