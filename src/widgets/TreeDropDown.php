<?php

namespace sbs\widgets;

use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Class DropDownTree
 *
 * @package sbs\widgets
 */
class TreeDropDown extends Widget
{
    /**
     * @var Model|null the data model that this widget is associated with.
     */
    public $model;

    /**
     * @var string|null the model attribute that this widget is associated with.
     */
    public $attribute;

    /**
     * @var string|null the input name. This must be set if [[model]] and [[attribute]] are not set.
     */
    public $name;

    /**
     * @var string the selected value.
     */
    public $value;

    /**
     * @var array The HTML attribute options for the input tag.
     *
     * @see yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var yii\db\ActiveQuery the query that will be used as the data source for the TreeSelect.
     * For example: `TreeModel::find()->where(['parent_id' => null])`
     */
    public $query;

    /**
     * @var mixed list of IDs to exlude from select
     */
    public $exclude;

    /**
     * @var string
     */
    public $idAttribute = 'id';

    /**
     * @var string name of select option attribute.
     */
    public $nameAttribute = 'name';

    /**
     * @var string name of parent relation attribute.
     */
    public $parentIdAttribute = 'parent_id';

    /**
     * @var array list of items in the nav widget.
     */
    private $items = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->name === null && $this->attribute === null && !$this->hasModel()) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        $this->exclude = is_array($this->exclude) ?: [$this->exclude];

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->items = $this->buildTree($this->query->all());

        if ($this->hasModel()) {
            Html::addCssClass($this->options, 'form-control');

            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
    }

    /**
     * @return boolean whether this widget is associated with a data model.
     */
    protected function hasModel()
    {
        return $this->model instanceof Model && $this->attribute !== null;
    }

    private function buildTree($tree, $pass = 0)
    {
        $result = [];
        foreach ($tree as $node) {
            if (in_array($node->{$this->idAttribute}, $this->exclude)) {
                continue;
            }
            $result[$node->{$this->idAttribute}] = str_repeat('-', $pass) . ' ' . $node->{$this->nameAttribute};

            if ($this->childrenQuery($node)->count()) {
                $result = ArrayHelper::merge($result, $this->buildTree($this->childrenQuery($node)->all(), $pass + 1));
            }
        }

        return $result;
    }

    /**
     * @param $node yii\db\Model
     *
     * @return yii\db\ActiveQuery
     */
    private function childrenQuery($node)
    {
        return $node->hasMany(get_class($node), [$this->parentIdAttribute => $this->idAttribute]);
    }
}
