<?php

namespace backend\controllers;

use backend\models\User;

class UserController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        $rows =User::find()->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new User();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码处理
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model = User::findOne(['id'=>$id]);
        //密码
        $model->password_hash = '';
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码处理
                $model->password = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除
    public function actionDelete($id){
        $model = User::findOne(['id'=>$id]);

    }
}
