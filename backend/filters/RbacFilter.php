<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{
//过滤器
    public function BeforeAction($action){
        if(!\Yii::$app->user->can($action->uniqueId)){
            if(\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            throw new HttpException('403','对不起,你没有权限访问该操作');
        }
        return true;
    }
}