<?php
namespace frontend\controllers;

use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\Member;
use frontend\models\SignatureHelper;
use Yii;
use yii\base\InvalidParamException;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Cookie;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $rows = GoodsCategory::find()->where([ GoodsCategory::tableName().'.parent_id'=>0])->joinWith('subCategory')->all();// 这里的where条件需要加入主表表明来区别所属表
//var_dump( $rows); exit;

        //保存到redis 中
        $redis = new \Redis();
        $redis->open('127.0.0.1');
        $html = $redis->get('category_view');
        if(!$html) {

            $html .= '<div class="cat_bd">';
            foreach ($rows as $k1 => $row) {
                ;
                $html .= '<div class="cat ' . ($k1 ? '' : 'item1') . '">';
                $html .= '<h3><a href="' . \yii\helpers\Url::to(["site/list", "id" => $row->id]) . '">' . $row->name . '</a> <b></b></h3>';
                $html .= '<div class="cat_detail">';

                foreach ($row->subCategory as $k2 => $item) {

                    $html .= '<dl class="' . ($k2 ? '' : 'dl_1st') . '"> ';
                    $html .= '<dt><a href="' . \yii\helpers\Url::to(['site/list', 'id' => $item->id]) . '">' . $item->name . '</a></dt>';
                    $html .= '<dd>';
                    foreach ($item->subCategory as $e) {

                        $html .= '<a href="' . \yii\helpers\Url::to(['site/list', 'id' => $e->id]) . '">' . $e->name . '</a>';
                    }
                    $html .= '</dd>';
                    $html .= '</dl>';
                }
                $html .= '<dl>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $redis->set('category_view',$html,24*3600);
        }
        return $this->render('index',['html'=>$html]);
       // return $this->render('index',['rows'=>$rows]);
    }

    public function actionList($id)
    {
        $query = Goods::find();
        $pages = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>1
        ]);
        //按分类层次显示数据
        $category = \backend\models\GoodsCategory::findOne(['id'=>$id]);
        $ids = [];
        if($category->depth ==2){ //三级分类
            $ids[] = $id;
        }else{//一级 二级分类
            //$category_id= \backend\models\GoodsCategory::find()->select('id')->where(['parent_id'=>$id])->asArray()->all();
            $category_id =$category->children()->select('id')->andWhere(['depth'=>2])->asArray()->all();
           $ids = ArrayHelper::map($category_id,'id','id');
        }
        $rows = $query->where(['in','goods_category_id',$ids])
            ->limit($pages->limit)->offset($pages->offset)->all();

       // $rows = Goods::find()->where(['goods_category_id'=>$id])->all();
        return $this->render('list',['rows'=>$rows,'pages'=>$pages,'html'=> $this-> getGoodsCategory()]);
    }

    public function getGoodsCategory(){
        $redis = new \Redis();
        $redis->open('127.0.0.1');
        $html = $redis->get('category_view');
        return $html;
    }

    public function actionGoods($id)
    {
        $rows = Goods::find()->where(['id'=>$id])->all();
            //var_dump($rows);exit;
        $rows[0]->view_times ++;
        $rows[0]->save();
        return $this->render('goods',['rows'=>$rows,'html'=> $this-> getGoodsCategory()]);
    }
    //添加商品到购物车
    public function actionAddCart($goods_id,$amount){
        //未登录 购物车数据 保存到 cookie 中
        if(Yii::$app->user->isGuest){
            //看 cookie 中 有无数据
            $cookies = Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
            }else{
                $cart = [];
            }
            //保存 cart 数据
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
           //写cookie
            $cookies = Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie ->value = serialize($cart);
            $cookies->add($cookie);
        }else {
            //登录保存到 数据库中
            $member_id = Yii::$app->user->id;
            $carts = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);
            if ($carts) {
                $carts->amount += $amount;
                $carts->save(false);
            } else {
                $model = new Cart();
                $model->goods_id = $goods_id;
                $model->member_id = $member_id;
                $model->amount = $amount;
                $model->save(false);
            }
        }
        return $this->redirect(['site/cart']);
    }
    //购物车
    public function actionCart(){
        //未登录 购物车数据 从 cookie 中取
        if(Yii::$app->user->isGuest){
           // $cookie = new Cookie();
           $cookies = Yii::$app->request->cookies;
                  if($cookies->has('cart')){
                      $value = $cookies->getValue('cart');
                      $cart = unserialize($value);
                  }else{
                      $cart = [];
                  }
          // var_dump($cart);exit;
            $ids = array_keys($cart);
        }else{
            //登录从 数据库中取
            $member_id = Yii::$app->user->id;
            $carts =Cart::find()->where(['member_id'=>$member_id])->all();
            //var_dump($goods_id);
           // var_dump($amount);exit;
            $cart = ArrayHelper::map($carts,'goods_id','amount');
            $ids = ArrayHelper::map($carts,'goods_id','goods_id');
        }
        $rows = Goods::find()->where(['in','id',$ids])->all();
        //var_dump($rows);exit;
        return $this->render('cart',['rows'=>$rows,'cart'=>$cart]);
    }
    //改变Cart 数量
    public function actionCartAmount(){
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        //未登录
        if(Yii::$app->user->isGuest){
            $cookies = Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
            }else{
                $cart = [];
            }
            $cookies = Yii::$app->response->cookies;
            $cart[$goods_id] = $amount;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie ->value = serialize($cart);
            $cookies->add($cookie);
        }else{
            //登录保存到 数据库中
            $model = new Cart();
            $model->goods_id = $goods_id;
            $model->member_id = Yii::$app->user->id;
            $model->amount = $amount;
            if ($model->validate()) {
                $model->save(false);
            } else {
                var_dump($model->getErrors());
            }
        }
    }
    //删除cart 数据
    public function actionCartDel($id){
        if(Yii::$app->user->isGuest){
            $cookies = Yii::$app->request->cookies;
            if($cookies->has('cart')) {
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
                foreach ($cart as $k => $v) {
                    if($k == $id){
                        unset($cart[$k]);
                    }
                }
                $cookies = Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'cart';
                $cookie ->value = serialize($cart);
                $cookies->add($cookie);
            }

        }else{
            $rows =Cart::find()->where(['goods_id'=>$id])->all();
            foreach ($rows as $row){
                $row->delete();
            }
        }

        //var_dump($model);exit;
        echo json_encode(1);
    }
    //验证手机验证码
    public function actionCheckCaptcha($tel,$captcha){
        $redis = new \Redis();
        $redis->open('127.0.0.1');
        $redis_code = $redis->get('code_'.$tel);
        if($redis_code==false){
            return 'false';
        }else{
            if($redis_code==$captcha){
                return 'true';
            }else{
                return 'false';
            }
        }
    }
    public function actionSms($tel){

        //正则判断电话号码
        if(!preg_match("/^1[34578]{1}\d{9}$/",$tel)){
            echo "手机号码不正确";
        }
        $code = rand(1000,9999);
        $result = Yii::$app->sms->send($tel,['code'=>$code]);
        var_dump($result->Code);
        if($result->Code=='OK'){
            //把短信验证码保存到redis 中
            $redis = new \Redis();
            $redis->open('127.0.0.1');
            $redis->set('code_'.$tel,$code,30*60); //保存30分钟
            echo 'true';
        }else{
            echo '短信发送失败';
        }

/*        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIqveIXVQSErFT";
        $accessKeySecret = "ZTShaJFakuT7hfZipsBVMct1S6obHO";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = "15282899783";

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "橙子的店";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_120130255";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => rand(1000,9999),
           // "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
       // $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );

        var_dump($content);*/
    }
/*    public function init()
    {
        $this->enableCsrfValidation = false;
    }*/

    public function actionRegister()
    {

        $request = \Yii::$app->request;
        $model = new Member();

        if($request->isPost){
           // var_dump($model);exit;
            $model->load($request->post(),'');

            if($model->validate()){
                //密码处理
                // var_dump($model->password_hash);exit;
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
                //var_dump($model->password_hash);exit;
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save(false);
                \Yii::$app->session->setFlash('success','注册成功');
                return $this->redirect(['site/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('register');
    }

    public function actionCheckUser($username){
         $username = Member::findOne(['username'=>$username]);
         if($username){
             echo 'false';
         }else{
             echo 'true';
         }

    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
