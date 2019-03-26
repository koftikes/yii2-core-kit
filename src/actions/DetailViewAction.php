<?php

namespace sbs\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\widgets\DetailView;

class DetailViewAction extends Action
{
    /**
     * @var string the name of the action view.
     */
    public $view = 'view';

    /**
     * @var string class name which will be show detail info.
     * The class must implement [[DetailView]].
     * This property must be set.
     */
    public $detailClass;

    /**
     * @var array a list of attributes to be displayed in the detail view.
     * Each array element represents the specification for displaying one particular attribute.
     */
    public $detailConfig = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->detailClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$detailClass must be set.');
        }
        parent::init();
    }

    /**
     * @param $id
     * @return mixed
     * @throws InvalidConfigException
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        $this->detailConfig['model'] = $model;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(self::EVENT_SUCCESS, 'Record was successfully saved.');
        }

        $widget = $this->createWidget();
        $this->handler(self::EVENT_RESPONSE, ['widget' => $widget]);

        return $this->controller->render($this->view, ['widget' => $widget]);
    }

    /**
     * @return DetailView
     * @throws InvalidConfigException
     */
    protected function createWidget()
    {
        /** @var DetailView $widget */
        $widget = Yii::createObject(array_merge(['class' => $this->detailClass], $this->detailConfig));

        foreach ($widget->attributes as $k => $i) {
            if (!isset($i['value']) || !is_string($i['value'])) {
                continue;
            }
            if (strpos($i['value'], '$model') !== false || strpos($i['value'], '$data') !== false) {
                $val = str_replace(['$model', '$data'], '$widget->model', $i['value']);
                ob_start();
                eval("echo $val;");
                $value = ob_get_clean();
                $widget->attributes[$k]['value'] = $value;
            }
        }

        return $widget;
    }
}
