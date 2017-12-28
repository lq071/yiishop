<?php
namespace backend\models;

use yii\base\Model;
use yii\rbac\Permission;

class PermissionForm extends Model{
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name','description'],'required'],
            [['name'],'checkName','on'=>self::SCENARIO_ADD],
            [['name'],'checkEdit','on'=>self::SCENARIO_EDIT],
        ];
    }
    public function checkName(){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($this->name);
        if($permission){
            $this->addError('name','权限名已经存在');
        }
    }
    public function checkEdit(){
        $authManager = \Yii::$app->authManager;
        $name = \Yii::$app->request->get('name');
        //修改前的权限名 和 修改后的权限名的同
        if($name != $this->name){
            $permission = $authManager->getPermission($this->name);
            if($permission){
                $this->addError('name','权限名已经存在');
            }
        }
    }
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'描述'
        ];
    }

    //保存权限
    public function save(){
        $authManager = \Yii::$app->authManager;
        $permission = new Permission();
        $permission->name = $this->name;
        $permission->description = $this->description;
        $authManager->add($permission);
    }
    //修改
    public function update($name){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $permission->name = $this->name;
        $permission->description = $this->description;
        $authManager->update($name,$permission);
    }
}