<?php

namespace sbs\actions;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class UpdateAction
 * @package backend\actions
 */
class UpdateAction extends FormProcessAction
{
    /**
     * {@inheritdoc}
     */
    public $view = 'update';

    /**
     * @param null $id
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function run($id = null)
    {
        if ($id == null) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', ['params' => ' id']));
        }

        return parent::run($id);
    }

    /**
     * @param ActiveRecord $model
     * @return array
     * @throws ServerErrorHttpException
     */
    public function success(ActiveRecord $model)
    {
        if (parent::success($model) === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update record by unknown reason.');
        }
    }
}
