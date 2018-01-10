<?php
namespace  frontend\controllers;

use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use Yii;
use yii\web\Controller;


class LoginController extends Controller {
    public $enableCsrfValidation = false;
//同步购物车
    public function syncBuyCar(){
        $member_id = Yii::$app->user->id;
        $cookies = Yii::$app->request->cookies;
        if($cookies->has('cart')){
            $value = $cookies->getValue('cart');
            $cart = unserialize($value);
            foreach($cart as $k=>$v){
                $carts = Cart::findOne(['goods_id' => $k, 'member_id' => $member_id]);
                if ($carts) {
                    $carts->amount += $v;
                    $carts->save(false);
                } else {
                    $model = new Cart();
                    $model->goods_id = $k;
                    $model->member_id = $member_id;
                    $model->amount = $v;
                    $model->save(false);
                }
            }
            $cookies = Yii::$app->response->cookies;
            $cookies->remove('cart');
        }
    }

    public function actionLogin(){
        $request = \Yii::$app->request;
        $model = new LoginForm();
       // var_dump($model);exit;
        if($request->isPost){
            $model->load($request->post(),'');
            //var_dump($model);exit;
            $ip = $request->getUserIP();
            if($model->validate()){
                if( $model->checkLogin($ip)){
                    //var_dump(\Yii::$app->user->identity);exit; // 登录信息
                    //同步cookie 购物车
                    $this->syncBuyCar();
                    \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['@web/index']);
                }else{
                    var_dump($model->getErrors());
                }
            }
        }
        return $this->render('login');
    }

    //退出
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['login/login']);
    }
}