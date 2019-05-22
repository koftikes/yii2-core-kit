<?php

namespace sbs\actions;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

/**
 * Class ExecuteColumn.
 *
 * To add a ExecuteColumn to the GridView, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => ExecuteColumn::class,
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 */
class ExecuteColumn extends DataColumn
{
    public $helperClass;

    public $helperMethod;

    /**
     * Returns the data cell value.
     *
     * @param mixed $model the data model
     * @param mixed $key   the key associated with the data model
     * @param int   $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]]
     *
     * @return mixed the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if (null !== $this->helperClass && null !== $this->helperMethod) {
            $helper = $this->helperClass;
            $method = $this->helperMethod;

            return $helper::$method(ArrayHelper::getValue($model, $this->attribute));
        }

        //TODO: Need to update for work with Closure
        if (\is_string($this->value) && (false !== \mb_strpos($this->value, '$model') || false !== \mb_strpos($this->value, '$data'))) {
            $val = \str_replace(['$model', '$data'], '$model', $this->value);
            \ob_start();
            eval("echo ${val};");
            $value = \ob_get_clean();

            return $value;
        }

        if (null !== $this->attribute) {
            return ArrayHelper::getValue($model, $this->attribute);
        }

        return null;
    }
}
