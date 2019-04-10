<?php

namespace sbs\actions;

use kartik\grid\DataColumn;
use yii\helpers\ArrayHelper;

/**
 * Class ExecuteColumn
 *
 * To add a ExecuteColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
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
 *
 * @package sbs\actions
 */
class ExecuteColumn extends DataColumn
{
    public $helperClass = null;

    public $helperMethod = null;

    /**
     * Returns the data cell value.
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->helperClass !== null && $this->helperMethod !== null) {
            $helper = $this->helperClass;
            $method = $this->helperMethod;

            return $helper::$method(ArrayHelper::getValue($model, $this->attribute));
        }

        if (strpos($this->value, '$model') !== false || strpos($this->value, '$data') !== false) {
            $val = str_replace(['$model', '$data'], '$model', $this->value);
            ob_start();
            eval("echo $val;");
            $value = ob_get_clean();

            return $value;
        }

        if ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }

        return null;
    }
}
