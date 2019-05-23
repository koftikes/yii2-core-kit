<?php

namespace sbs\actions;

use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class Action.
 */
class Action extends \yii\base\Action
{
    const EVENT_SUCCESS  = 'success';

    const EVENT_ERROR    = 'error';

    const EVENT_RESPONSE = 'response';

    /**
     * @var ActiveRecord class name of the model which will be handled by this action. The model class must implement [[ActiveRecordInterface]].
     */
    public $modelClass;

    public $handlers = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (null === $this->modelClass) {
            throw new InvalidConfigException(\get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @param mixed $id
     *
     * @throws NotFoundHttpException
     *
     * @return ActiveRecord the model found
     */
    public function findModel($id)
    {
        // @var $modelClass ActiveRecord
        $keys = $this->modelClass::primaryKey();
        if (\count($keys) > 1) {
            $values = \explode(',', $id);
            if (\count($keys) === \count($values)) {
                $model = $this->modelClass::findOne(\array_combine($keys, $values));
            }
        } elseif (null !== $id) {
            $model = $this->modelClass::findOne($id);
        }

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: ${id}");
    }

    /**
     * @param string $name
     * @param array  $param
     *
     * @throws InvalidConfigException
     */
    public function handler($name, $param = [])
    {
        if (isset($this->handlers[$name], $this->handlers[$name]['class'])) {
            $handler     = (\is_array($this->handlers[$name])) ? $this->handlers[$name] : [$this->handlers[$name]];
            $event       = new Event();
            $event->name = $name;
            if (null === $event->sender) {
                $event->sender = $this;
            }
            $event->data = $param;

            $object = Yii::createObject($handler);
            $object->run($event);
        }
    }
}
