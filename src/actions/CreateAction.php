<?php

namespace sbs\actions;

use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateAction.
 */
class CreateAction extends FormProcessAction
{
    /**
     * {@inheritdoc}
     */
    public $view = 'create';

    /**
     * @param ActiveRecord $model
     *
     * @throws ServerErrorHttpException
     *
     * @return array|bool
     */
    public function success(ActiveRecord $model)
    {
        if (false === parent::success($model) && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create record by unknown reason.');
        }

        if ($this->redirectToView) {
            $id = \implode(',', \array_values($model->getPrimaryKey(true)));

            return ['route' => [$this->viewAction, 'id' => $id]];
        }
    }
}
