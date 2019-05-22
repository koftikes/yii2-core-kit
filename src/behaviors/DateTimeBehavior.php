<?php

namespace sbs\behaviors;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * DateTimeBehavior automatically fills the specified attributes with the current datetime.
 * To use DateTimeBehavior, insert the following code to your ActiveRecord class:.
 *
 * ```php
 * use sbs\behaviors\DateTimeBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         DateTimeBehavior::class,
 *     ];
 * }
 * ```
 *
 * Class DateTimeBehavior
 */
class DateTimeBehavior extends TimestampBehavior
{
    /**
     * {@inheritdoc}
     */
    public $createdAtAttribute = 'create_date';

    /**
     * {@inheritdoc}
     */
    public $updatedAtAttribute = 'update_date';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->value = new Expression('NOW()');
        parent::init();
    }
}
