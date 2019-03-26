<?php

namespace sbs\actions;

use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateAction
 * @package sbs\actions
 */
class CreateAction extends FormProcessAction
{
    /**
     * {@inheritdoc}
     */
    public $view = 'create';

    /**
     * @param ActiveRecord $model
     * @return array
     * @throws ServerErrorHttpException
     */
    public function success(ActiveRecord $model)
    {
        if (parent::success($model) === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create record by unknown reason.');
        }

        if ($this->redirectToView) {
            $id = implode(',', array_values($model->getPrimaryKey(true)));

            return ['route' => [$this->viewAction, 'id' => $id]];
        }
    }
}
