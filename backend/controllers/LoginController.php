<?php
namespace backend\controllers;

use backend\models\LoginForm;
use yii\captcha\CaptchaAction;
use yii\web\Controller;

class LoginController extends Controller{
    //登录
    public function actionLogin(){
        $request = \Yii::$app->request;
        $model = new LoginForm();

        if($request->isPost){

            $model->load($request->post());

            $ip = $request->getUserIP();
            if($model->validate()){
                if( $model->checkLogin($ip)){
                   // var_dump(\Yii::$app->user->identity);exit; // 登录信息
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['goods/index']);
                }else{
                    var_dump($model->getErrors());
                }
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //退出
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['login/login']);
    }
    //验证码
    public function actions(){
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                //验证码设置
                'height'=>50,
                'minLength'=>4,
                'maxLength'=>4
            ]

        ];
    }


}