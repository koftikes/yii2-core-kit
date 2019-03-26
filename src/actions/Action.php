<?php

namespace sbs\actions;

use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

/**
 * Class Action
 * @package sbs\actions
 */
class Action extends \yii\base\Action
{
    const EVENT_SUCCESS = 'success';
    const EVENT_ERROR = 'error';
    const EVENT_RESPONSE = 'response';

    /**
     * @var string class name of the model which will be handled by this action.
     * The model class must implement [[ActiveRecordInterface]].
     * This property must be set.
     */
    public $modelClass;

    public $handlers = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @param $id
     * @return ActiveRecordInterface the model found
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        /* @var $modelClass ActiveRecordInterface */
        $keys = $this->modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $this->modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $this->modelClass::findOne($id);
        }

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }

    /**
     * @param $name
     * @param array $param
     * @throws InvalidConfigException
     */
    public function handler($name, $param = [])
    {
        if (isset($this->handlers[$name]) && isset($this->handlers[$name]['class'])) {
            $handler = (is_array($this->handlers[$name])) ? $this->handlers[$name] : [$this->handlers[$name]];
            $event = new Event;
            $event->name = $name;
            if ($event->sender === null) {
                $event->sender = $this;
            }
            $event->data = $param;

            $object = Yii::createObject($handler);
            $object->run($event);
        }
    }
}
