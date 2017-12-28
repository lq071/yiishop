<?php
namespace backend\models;

use yii\base\Model;

class PasswordForm extends Model{
    public $oldPassword;
    public $newPassword;
    public $rePassword;
}