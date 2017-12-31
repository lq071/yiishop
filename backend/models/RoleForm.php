<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public $name;
    public $description;
    public $permission;
    public function rules()
    {
        return [
            [['name','description','permission'],'required'],
            [['name'],'checkName','on'=>self::SCENARIO_ADD],
            [['name'],'checkEdit','on'=>self::SCENARIO_EDIT],
        ];
    }
    //验证角色名 添加
    public function checkName(){
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($this->name);
        if($role){
            $this->addError('name','该角色已经存在');
        }
    }
    //修改
    public function checkEdit(){
        $authManager = \Yii::$app->authManager;
        $name = \Yii::$app->request->get('name');
        //修改前的权限名 和 修改后的权限名的同
        if($name != $this->name){
            $role = $authManager->getRole($this->name);
            var_dump($role);exit;
            if($role){
                $this->addError('name','该角色已经存在');
            }
        }
    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'描述',
            'permission'=>'权限'
        ];
    }

}