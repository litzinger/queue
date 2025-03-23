<?php

namespace BoldMinded\Queue\ControlPanel\Routes;

use BoldMinded\Queue\Actions\ActionUrl;
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

        $urlBase = '/' . ee('CP/URL')->make('addons/settings/queue');
        $urlQueueStatus = (new ActionUrl)->getActionUrl('Queue', 'fetchQueueStatus');
        $urlPurgeAllPendingJobs = (new ActionUrl)->getActionUrl('Queue', 'purgeAllPendingJobs');
        $urlGetFailedJob = (new ActionUrl)->getActionUrl('Queue', 'getFailedJob');
        $urlRetryFailedJob = (new ActionUrl)->getActionUrl('Queue', 'retryFailedJob');
        $urlDeletedFailedJob = (new ActionUrl)->getActionUrl('Queue', 'deleteFailedJob');
        $urlCron = (new ActionUrl)->getActionUrl('Queue', 'queueCron');

        $config = ee()->config->item('queue') ?: [];
        $driver = $config['driver'] ?? 'database';

        $variables = [
            'size' => $queueStatus->getSize(),
            'assetPath' => URL_THIRD_THEMES . '/queue/assets/',
            'urlBase' => $urlBase,
            'urlQueueStatus' => $urlQueueStatus,
            'urlPurgeAllPendingJobs' => $urlPurgeAllPendingJobs,
            'urlGetFailedJob' => $urlGetFailedJob,
            'urlRetryFailedJob' => $urlRetryFailedJob,
            'urlDeleteFailedJob' => $urlDeletedFailedJob,
            'csrfToken' => CSRF_TOKEN,
            'queueDriver' => ucfirst($driver),
        ];

        ee('CP/Alert')->makeInline('shared-form')
            ->asAttention()
            ->cannotClose()
            ->withTitle(lang('queue_cron_setup'))
            ->addToBody(sprintf(lang('queue_cron_setup_desc'), $urlCron, $urlCron))
            ->now();

        $this->setBody('Index', $variables);

        return $this;
    }
}
