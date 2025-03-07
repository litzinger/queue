<?php

namespace BoldMinded\Queue\Actions;

class ActionUrl
{
    public function getActionUrl(string $class, string $method, array $params = []): string
    {
        $actionId = $this->fetchActionId($class, $method);

        if (!$actionId) {
            return false;
        }

        $siteIndex = ee()->functions->fetch_site_index(false, false);
        $additional = rtrim('&' . http_build_query($params, '', '&'), '&');

        return str_replace('?', '', $siteIndex) . '?ACT=' . $actionId . $additional;
    }

    protected function fetchActionId(string $class, string $method): int
    {
        /** @var \ExpressionEngine\Model\Addon\Action $action */
        $action = ee('Model')->get('Action')
            ->filter('class', '=', $class)
            ->filter('method', '=', $method)
            ->first();

        if ($action === null) {
            return 0;
        }

        return $action->action_id;
    }
}
