<?php

namespace sbs\behaviors;

use Yii;
use yii\web\User;
use yii\base\Behavior;
use yii\behaviors\TimestampBehavior;

/**
 * LastVisitBehavior automatically fills the specified attribute with the current datetime.
 * To use behavior, insert the following code to your config file:
 *
 * ```php
 *
 * use sbs\behaviors\LastVisitBehavior;
 *
 *  'components' => [
 *      'user' => [
 *          //...
 *          'identityClass' => User::class,
 *          'as afterLogin' => LastVisitBehavior::class,
 *      ],
 *  ]
 * ```
 *
 * Class LastVisitBehavior
 * @package sbs\behaviors
 */
class LastVisitBehavior extends Behavior
{
    /**
     * @var string the attribute that will receive last visit datetime.
     */
    public $attribute = 'last_visit';

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin',
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterLogin($event)
    {
        $user = $event->identity;
        if (!$user->hasMethod('touch')) {
            throw new \BadMethodCallException('For "' . __CLASS__ . '" require to use "'
                . TimestampBehavior::class . '" in "' . Yii::$app->components['user']['identityClass'] . '".');
        }
        $user->touch($this->attribute);
        $user->save(false);
    }
}
