<?php

namespace sbs\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * Class GridViewAction.
 */
class GridViewAction extends Action
{
    public $filterModel;

    /**
     * @var string the name of the action view
     */
    public $view = 'index';

    /**
     * @var string class name which will be show detail info.
     *             The class must implement [[BaseListView]].
     *             This property must be set.
     */
    public $gridClass;

    /**
     * @var array a list of attributes to be displayed in the detail view.
     *            Each array element represents the specification for displaying one particular attribute.
     */
    public $gridConfig = [];

    public $dataProvider = [];

    public $withFilters = false;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (null === $this->gridClass) {
            throw new InvalidConfigException(\get_class($this) . '::$gridClass must be set.');
        }
        parent::init();
    }

    /**
     * @throws InvalidConfigException
     *
     * @return string
     */
    public function run()
    {
        $widget = $this->createWidget();
        $this->handler(self::EVENT_RESPONSE, ['widget' => $widget]);

        return $this->controller->render($this->view, ['widget' => $widget]);
    }

    /**
     * @throws InvalidConfigException
     *
     * @return \yii\grid\GridView
     */
    protected function createWidget()
    {
        $this->gridConfig['dataProvider'] = $this->getDataProvider();
        /** @var \yii\grid\GridView $grid */
        $grid = Yii::createObject(\array_merge(['class' => $this->gridClass], $this->gridConfig));

        return $grid;
    }

    /**
     * Create data provider instance with search query applied.
     *
     * @throws InvalidConfigException
     *
     * @return ActiveDataProvider
     */
    public function getDataProvider()
    {
        /** @var ActiveQueryInterface $query */
        $query = $this->modelClass::find();

        if ($this->withFilters) {
            /** @var ActiveRecord $filterModel */
            $filterModel             = new $this->modelClass();
            $filterModel->attributes = [];

            if (!\method_exists($filterModel, 'applyFilter')) {
                throw new InvalidConfigException(\get_class($filterModel) . ' must define a "applyFilter()" method.');
            }

            if (Yii::$app->request->queryParams && \method_exists($filterModel, 'applyFilter')) {
                $filterModel->load(Yii::$app->request->queryParams);
                $query = $filterModel->applyFilter($query);
            }

            $this->gridConfig['filterModel'] = $filterModel;
        }

        $this->dataProvider['query'] = $query;

        return new ActiveDataProvider($this->dataProvider);
    }
}
