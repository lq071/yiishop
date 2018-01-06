<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = 'on';
    public $code;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'string'],
            // password is validated by validatePassword()
           // ['password_hash', 'validatePassword'],
            ['code','captcha','captchaAction'=>'site/captcha'],
            ['code','safe']
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
        $userInfo = Member::findOne(['username'=>$this->username]);
        if(!$userInfo){
            $this->addError('username','用户名不存在');
            return false;
        }else{
            if(!Yii::$app->security->validatePassword($this->password,$userInfo->password_hash)                  ){
                $this->addError('password','密码不正确');
                return false;
            }else{
                $userInfo->last_login_time = time();
                $userInfo->last_login_ip = $ip;
                $userInfo->save(false);
                Yii::$app->user->login($userInfo,$this->rememberMe == 'on' ? 7*24*3600 :0);
                return true;
            }
        }
    }



}
