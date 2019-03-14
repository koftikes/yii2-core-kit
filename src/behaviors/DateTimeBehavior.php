<?php

namespace sbs\behaviors;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class DateTimeBehavior extends TimestampBehavior
{
    public $createdAtAttribute = 'create_date';
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