<?php
namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;

class RbacController extends Controller{
    //添加权限
    public function actionAddP(){
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['rbac/index']);
            }
        }
        return $this->render('add-p',['model'=>$model]);
    }
    //权限列表
    public function actionIndex(){
        $authManage = \Yii::$app->authManager;
       // var_dump($authManage);exit;
        $rows = $authManage->getPermissions();
        return $this->render('index',['rows'=>$rows]);
    }
    //修改权限
    public function actionEditP($name){
        $model=new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_EDIT;
        $request = \Yii::$app->request;
        $authManager = \Yii::$app->authManager;
        //回显
        $permission = $authManager->getPermission($name);
        $model->name = $permission->name;
        $model->description = $permission->description;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->update($name);
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['rbac/index']);
            }
        }
        return $this->render('add-p',['model'=>$model]);
    }
    //删除权限
    public function actionDeleteP($name){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $authManager ->remove($permission);
        echo json_encode(1);
    }
    //角色列表
    public function actionIndexRole(){
        $authManager = \Yii::$app->authManager;
        // var_dump($authManage);exit;
        $rows = $authManager->getRoles();
        return $this->render('index-role',['rows'=>$rows]);
    }
    //添加角色
    public function actionAddRole(){
        $model = new RoleForm();
        $authManager = \Yii::$app->authManager;
        $request = \Yii::$app->request;
        $permission =$authManager->getPermissions();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $role = new Role();
                $role->name = $model->name;
                $role->description = $model->description;
                $authManager->add($role);
                $permissions = $model->permission;
                foreach($permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    $authManager->addChild($role,$permission);
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
        return $this->render('add-role',['model'=>$model,'permission'=>$permission]);
    }
    //修改角色
    public function actionEditRole($name){
        $model = new RoleForm();
        $authManager = \Yii::$app->authManager;
        $request = \Yii::$app->request;
        $permissions =$authManager->getPermissions();
        $role = $authManager->getRole($name);
       // var_dump($role);exit;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                 $role->name = $model->name;
                 $role->description = $model->description;
                 //处理角色和权限的关系
                $authManager->removeChildren($role);
                foreach($model->permission as $v){
                    $permission = $authManager->getPermission($v);
                    $authManager->addChild($role,$permission);
                }
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
        //回显
        $model->name = $role->name;
        $model->description = $role->description;
        $model->permission =[];
        $permission = $authManager->getPermissionsByRole($name);
        foreach ($permission as $v){
            $model->permission[] = $v->name;
        }
        return $this->render('add-role',['model'=>$model,'permission'=>$permissions]);
    }
    //删除
    public function actionDeleteRole($name){
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        //清除权限
        $authManager->removeChildren($role);
        //清除角色
        $authManager->remove($role);
        echo json_encode(1);
    }
}