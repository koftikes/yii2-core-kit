<?php

namespace sbs\actions;

use Yii;
use yii\base\Component;
use yii\base\Event;

class Redirect extends Component
{
    public $route = 'index';

    public $paramsMap = [];

    /**
     * @param Event $event
     */
    public function run(Event $event)
    {
        $route = isset($event->data['route']) ? $event->data['route'] : $this->route;
        $route = (is_array($route)) ? $route : [$route];
        $params = [];

        foreach ($this->paramsMap as $source => $target) {
            if ($_REQUEST[$source]) {
                $params[$target] = $_REQUEST[$source];
            }
        }
        $params = array_merge($route, $params);

        Yii::$app->controller->redirect(Yii::$app->urlManager->createUrl($params));
    }
}
