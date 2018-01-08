<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PasswordForm;
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
        $model->scenario = User::SCENARIO_ADD;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码处理
               // var_dump($model->password_hash);exit;
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //var_dump($model->password_hash);exit;
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save(false);
                //用户和角色的关系
                $authManager = \Yii::$app->authManager;
                if(is_array($model->role)){
                    foreach ($model->role as $v){
                        $role = $authManager->getRole($v);
                        if($role){
                            $authManager->assign($role,$model->id);
                        }
                    }
                }
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
        $authManager = \Yii::$app->authManager;
        $model = User::findOne(['id'=>$id]);
        //密码
        $model->password_hash = '';
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码处理
               $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);

                $model->save();
                $authManager->revokeAll($id);
                if(is_array($model->role)){
                    foreach ($model->role as $v){
                        $role = $authManager->getRole($v);
                        if($role){
                            $authManager->assign($role,$id);
                        }
                    }
                }
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        //回显
        $model->role =[];
        $role = $authManager->getRolesByUser($id);
        foreach ($role as $v){
            $model->role[] = $v->name;
        }
        return $this->render('add', ['model' => $model]);
    }
    //修改密码
    public function actionEditPwd(){
      //  var_dump(\Yii::$app->user->identity);exit;
        $model = new PasswordForm();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码处理
                $user = User::findOne(['id'=>\Yii::$app->user->identity->getId()]);
                $user->password_hash = \Yii::$app->security->generatePasswordHash($model->newPassword);
                $user->save();
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('password',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $model = User::findOne(['id'=>$id]);
        $model->delete();
        echo json_encode(1);
    }
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ],
        ];
    }
}
