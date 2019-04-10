<?php

namespace sbs\actions;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class FormProcessAction
 * @package sbs\actions
 */
class FormProcessAction extends Action
{
    /**
     * @var string the name of the action view. This property must be set.
     */
    public $view;

    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';

    public $redirectToView = false;

    /**
     * @param integer $id
     * @return array|string
     */
    public function run($id = null)
    {
        /** @var ActiveRecord $model */
        if ($id === null) {
            $model = new $this->modelClass();
        } else {
            $model = $this->findModel($id);
        }
        $model->scenario = $this->scenario;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }

            if ($model->validate()) {
                $params = $this->success($model);
                $params = (is_array($params)) ? $params : [$params];
                $this->handler(self::EVENT_SUCCESS, $params);
            } else {
                $this->error($model);
                $this->handler(self::EVENT_ERROR);
            }
        }

        $this->handler(self::EVENT_RESPONSE, ['model' => $model]);

        return $this->response($model);
    }

    /**
     * @param ActiveRecord $model
     * @return bool
     */
    protected function success(ActiveRecord $model)
    {
        if ($status = $model->save(false)) {
            Yii::$app->session->setFlash(self::EVENT_SUCCESS, 'Record was successfully saved.');
        }

        return $status;
    }

    /**
     * @param ActiveRecord $model
     */
    protected function error(ActiveRecord $model)
    {
    }

    /**
     * @param ActiveRecord $model
     * @return string
     */
    protected function response(ActiveRecord $model)
    {
        if (Yii::$app->request->isAjax) {
            return Yii::$app->controller->renderAjax($this->view, ['model' => $model]);
        }

        return Yii::$app->controller->render($this->view, ['model' => $model]);
    }
}
