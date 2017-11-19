<?php

namespace sbs\components;

use yii\db\Migration;

class DbMigration extends Migration
{
    /**
     * get options for schema
     *
     * @return string options
     */
    public function getOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        return $tableOptions;
    }
}
