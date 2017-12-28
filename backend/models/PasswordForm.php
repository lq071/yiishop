<?php
namespace backend\models;

use yii\base\Model;

class PasswordForm extends Model{
    public $oldPassword;
    public $newPassword;
    public $rePassword;

    public function rules()
    {
        return [
            [['oldPassword','newPassword','rePassword'],'required'],
            ['oldPassword','checkOldPassword'],
            [['rePassword'], 'compare','compareAttribute'=>'newPassword'],
        ];
    }
    //验证旧密码
    public function checkOldPassword(){
        //var_dump(\Yii::$app->user->identity);exit;
        if(!\Yii::$app->security->validatePassword($this->oldPassword,\Yii::$app->user->identity->password_hash)){
            $this->addError('oldPassword','密码错误');
        }
    }
    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'rePassword'=>'确认密码'
        ];
    }
}