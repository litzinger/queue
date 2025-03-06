<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use ExpressionEngine\Service\Addon\Mcp;

class Queue_mcp extends Mcp
{
    protected $addon_name = 'queue';
}
