<?php

namespace BoldMinded\Queue\ControlPanel\Routes;

use ExpressionEngine\Service\Addon\Controllers\Mcp\AbstractRoute;

class Index extends AbstractRoute
{
    /**
     * @var string
     */
    protected $route_path = 'index';

    /**
     * @var string
     */
    protected $cp_page_title = 'Index';

    /**
     * @param false $id
     * @return AbstractRoute
     */
    public function process($id = false)
    {
        $this->addBreadcrumb('index', 'Index');

        $queueStatus = ee('queue:QueueStatus');

        $variables = [
            'size' => $queueStatus->getSize(),
        ];

        $this->setBody('Index', $variables);

        return $this;
    }
}
