<?php

namespace sbs\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\grid\GridView;

/**
 * Class GridViewAction
 * @package sbs\actions
 */
class GridViewAction extends Action
{
    public $filterModel;

    /**
     * @var string the name of the action view.
     */
    public $view = 'index';

    /**
     * @var string class name which will be show detail info.
     * The class must implement [[BaseListView]].
     * This property must be set.
     */
    public $gridClass;

    /**
     * @var array a list of attributes to be displayed in the detail view.
     * Each array element represents the specification for displaying one particular attribute.
     */
    public $gridConfig = [];

    public $dataProvider = [];

    public $withFilters = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->gridClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$gridClass must be set.');
        }
        parent::init();
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run()
    {
        $widget = $this->createWidget();
        $this->handler(self::EVENT_RESPONSE, ['widget' => $widget]);

        return $this->controller->render($this->view, ['widget' => $widget]);
    }

    /**
     * @return GridView
     * @throws InvalidConfigException
     */
    protected function createWidget()
    {
        $this->gridConfig['dataProvider'] = $this->getDataProvider();
        return Yii::createObject(array_merge(['class' => $this->gridClass], $this->gridConfig));
    }

    /**
     * Create data provider instance with search query applied
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function getDataProvider()
    {
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        /** @var ActiveQueryInterface $query */
        $query = $modelClass::find();

        if ($this->withFilters) {
            /** @var ActiveRecord $filterModel */
            $filterModel = new $modelClass;
            $filterModel->attributes = [];

            if (!method_exists($filterModel, 'applyFilter')) {
                throw new InvalidConfigException(get_class($filterModel) . ' must define a "applyFilter()" method.');
            }

            if (Yii::$app->request->queryParams && method_exists($filterModel, 'applyFilter')) {
                $filterModel->load(Yii::$app->request->queryParams);
                $query = $filterModel->applyFilter($query);
            }

            $this->gridConfig['filterModel'] = $filterModel;
        }

        $this->dataProvider['query'] = $query;

        return new ActiveDataProvider($this->dataProvider);
    }
}
