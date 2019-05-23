<?php

namespace sbs\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Class DeleteAction.
 */
class DeleteAction extends Action
{
    /**
     * Deletes a model.
     *
     * @param mixed $id id of the model to be deleted
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function run($id)
    {
        try {
            $model = $this->findModel($id);
            if (false === $model->delete()) {
                throw new ServerErrorHttpException('Failed to delete record by unknown reason.');
            }
            Yii::$app->session->setFlash(self::EVENT_SUCCESS, 'Record was deleted.');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash(self::EVENT_ERROR, $e->getMessage());
        }

        $this->handler(self::EVENT_SUCCESS);
    }
}
