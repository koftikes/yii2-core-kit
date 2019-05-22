<?php
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 *
 * @var string the new migration class name without namespace
 * @var string $namespace the new migration class namespace
 */
echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use sbs\components\DbMigration;

/**
 * Class <?= $className . "\n"; ?>
 */
class <?= $className; ?> extends DbMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "<?= $className; ?> cannot be reverted.\n";

        return false;
    }
}
