<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permission;
    public function rules()
    {
        return [
          [['name','description','permission'],'required'],
        ];
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