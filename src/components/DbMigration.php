<?php

namespace sbs\components;

use yii\db\Migration;

class DbMigration extends Migration
{
    /**
     * Get options for schema.
     *
     * @return null|string
     */
    public function getOptions()
    {
        $tableOptions = null;
        if ('mysql' === $this->db->driverName) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        return $tableOptions;
    }
}
