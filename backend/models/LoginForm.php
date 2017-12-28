<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password_hash;
    public $rememberMe = true;
    public $code;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password_hash','code'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
           // ['password_hash', 'validatePassword'],
            ['code','captcha','captchaAction'=>'login/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'code'=>'验证码',
            'rememberMe'=>'记住我',
        ];
    }

    public function checkLogin($ip){
        $userInfo = User::findOne(['username'=>$this->username]);
        if(!$userInfo){
            $this->addError('username','用户名不存在');
            return false;
        }else{

            if(!Yii::$app->security->validatePassword($this->password_hash,$userInfo->password_hash)            ){
                $this->addError('password_hash','密码不正确');
                return false;
            }else{
                $userInfo->last_login_time = time();
                $userInfo->last_login_ip = $ip;
                $userInfo->save(false);
                Yii::$app->user->login($userInfo,$this->rememberMe ? 7*24*3600 :0);
                return true;
            }
        }
    }



}
