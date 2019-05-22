<?php

namespace sbs\actions;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class UpdateAction.
 */
class UpdateAction extends FormProcessAction
{
    /**
     * {@inheritdoc}
     */
    public $view = 'update';

    /**
     * @param mixed $id
     *
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     *
     * @return array|string
     */
    public function run($id = null)
    {
        if (null === $id) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', ['params' => ' id']));
        }

        return parent::run($id);
    }

    /**
     * @param ActiveRecord $model
     *
     * @throws ServerErrorHttpException
     *
     * @return bool|void
     */
    public function success(ActiveRecord $model)
    {
        if (false === parent::success($model) && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update record by unknown reason.');
        }
    }
}
