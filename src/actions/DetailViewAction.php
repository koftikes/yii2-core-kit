<?php

namespace sbs\actions;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Class DetailViewAction.
 */
class DetailViewAction extends Action
{
    /**
     * @var string the name of the action view
     */
    public $view = 'view';

    /**
     * @var string class name which will be show detail info. The class must implement [[DetailView]].
     */
    public $detailClass;

    /**
     * @var array a list of attributes to be displayed in the detail view.
     *            Each array element represents the specification for displaying one particular attribute.
     */
    public $detailConfig = [];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (null === $this->detailClass) {
            throw new InvalidConfigException(\get_class($this) . '::$detailClass must be set.');
        }

        parent::init();
    }

    /**
     * @param mixed $id
     *
     * @throws InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     *
     * @return string
     */
    public function run($id)
    {
        $model                       = $this->findModel($id);
        $this->detailConfig['model'] = $model;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(self::EVENT_SUCCESS, 'Record was successfully saved.');
        }

        $widget = $this->createWidget();
        $this->handler(self::EVENT_RESPONSE, ['widget' => $widget]);

        return $this->controller->render($this->view, ['widget' => $widget]);
    }

    /**
     * @throws InvalidConfigException
     *
     * @return \yii\widgets\DetailView
     */
    protected function createWidget()
    {
        /** @var \yii\widgets\DetailView $widget */
        $widget = Yii::createObject(\array_merge(['class' => $this->detailClass], $this->detailConfig));

        foreach ($widget->attributes as $k => $i) {
            if (!isset($i['value']) || !\is_string($i['value'])) {
                continue;
            }
            if (false !== \mb_strpos($i['value'], '$model') || false !== \mb_strpos($i['value'], '$data')) {
                $val = \str_replace(['$model', '$data'], '$widget->model', $i['value']);
                \ob_start();
                eval("echo ${val};");
                $value                           = \ob_get_clean();
                $widget->attributes[$k]['value'] = $value;
            }
        }

        return $widget;
    }
}
