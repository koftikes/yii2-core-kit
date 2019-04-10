<?php

namespace sbs\actions;

use Yii;
use yii\base\Component;
use yii\base\Event;

/**
 * Redirects the browser to the specified URL.
 *
 * @param string|array $route the URL to be redirected to. This can be in one of the following formats:
 * - a string representing a URL (e.g. "http://example.com")
 * - a string representing a URL alias (e.g. "@example.com")
 * - an array in the format of `[$route, ...name-value pairs...]` (e.g. `['site/index', 'ref' => 1]`)
 *
 * @param array $refererParams allowed take params from referrer URL. This can be in one of the following formats:
 * - an array in the format of `[name-param-one, name-param-two]` (e.g. `['page', 'per-page']`)
 * - an array in the format of `[name-param-one, new-name-param => old-name-param]` (e.g. `['page', 'parent' => 'id']`)
 *
 * @package sbs\actions
 */
class Redirect extends Component
{
    public $route = 'index';

    public $refererParams = [];

    /**
     * @param Event $event
     */
    public function run(Event $event)
    {
        $route = isset($event->data['route']) ? $event->data['route'] : $this->route;
        $route = (is_array($route)) ? $route : [$route];

        $params = [];
        foreach ($this->refererParams as $target => $source) {
            $params[is_string($target) ? $target : $source] = $this->getParam($source);
        }

        Yii::$app->controller->redirect(array_merge($route, $params));
    }

    /**
     * Returns the named parameter value.
     *
     * @param string $name the parameter name
     * @param mixed $defaultValue the default parameter value if the parameter does not exist.
     * @return mixed the parameter value
     */
    private function getParam($name, $defaultValue = null)
    {
        $referer = parse_url($_SERVER['HTTP_REFERER']);
        $referer_params = [];
        if (isset($referer['query'])) {
            parse_str($referer['query'], $referer_params);
        }

        return isset($referer_params[$name]) ? $referer_params[$name] : $defaultValue;
    }
}
