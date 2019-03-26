<?php

namespace sbs\actions;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

/**
 * Class DeleteAction
 * @package backend\actions
 */
class DeleteAction extends Action
{
    /**
     * Deletes a model.
     * @param mixed $id id of the model to be deleted.
     * @throws ServerErrorHttpException on failure.
     */
    public function run($id)
    {
        try {
            /* @var $model ActiveRecord */
            $model = $this->findModel($id);

            if ($model->delete() === false) {
                throw new ServerErrorHttpException('Failed to delete record by unknown reason.');
            }
            Yii::$app->session->setFlash(self::EVENT_SUCCESS, 'Record was deleted.');

        } catch (Exception $e) {
            Yii::$app->session->setFlash(self::EVENT_ERROR, $e->getMessage());
        }

        $this->handler(self::EVENT_SUCCESS);
    }
}
