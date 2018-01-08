<?php
namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrderController extends Controller{
    public $enableCsrfValidation = false;

    public function getGoodsCategory(){
        $redis = new \Redis();
        $redis->open('127.0.0.1');
        $html = $redis->get('category_view');
        return $html;
    }

    public function actionIndex(){
        //是否登录 没登录 跳到登录页面
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['login/login']);
        }else{
            $member_id = \Yii::$app->user->id;
            if(Cart::find()->where(['member_id' => $member_id])->all()){

                //地址
                $addresses = Address::find()->where(['member_id' => $member_id])->all();
                //购物车商品数据
                $carts = Cart::find()->where(['member_id'=>$member_id])->all();
                $cart = ArrayHelper::map($carts,'goods_id','amount');
                $ids = ArrayHelper::map($carts,'goods_id','goods_id');
                /*  foreach ($carts as $c){
                      //var_dump($amount);exit;
                      $goods_id = $c['goods_id'];
                      $goods = Goods::find()->where(['id'=>$goods_id])->all();
                  }*/
                $goods = Goods::find()->where(['in','id',$ids])->all();
                //var_dump($goods);exit;
                return $this->render('index',['addresses'=>$addresses,'goods'=>$goods,'cart'=>$cart]);
            }else{
                return $this->redirect(['site/cart']);
            }
        }
    }
    //添加订单
    public function actionAdd(){
        //保存数据到 订单表
        $request = \Yii::$app->request;
        $order = new Order();
        $member_id = \Yii::$app->user->id;
        if($request->isPost){
            //订单数据
            $order->load($request->post(),'');
            //根据 地址id 查询 地址 数据库 详细数据 插入 订单表中 防止订单表地址改变,
            $address = Address::findOne(['id'=>$order->address_id]);
            $order->member_id = $member_id;
            $order->name = $address->name;
            $order->province = $address->province;
            $order->city = $address->city;
            $order->area = $address->area;
            $order->address = $address->address;
            $order->tel = $address->tel;
            //配送方式
            $delivery = Order::$delivery;
            $order->delivery_name = $delivery[$order->delivery_id][0];
            $order->delivery_price = $delivery[$order->delivery_id][1];
            //支付方式
            $payment = Order::$payment;
            $order->payment_name = $payment[$order->payment_id][0];
            //订单金额
            $carts = Cart::find()->where(['member_id'=>$member_id])->all();
            //总金额
            $money = 0;
            foreach($carts as $cart){
                $goods = Goods::findOne(['id'=>$cart->goods_id]);
                //总金额
                $money += $goods->shop_price*$cart->amount;
            }
            $order ->total = $money + $order->delivery_price;
            //订单状态
            $order->status = 1;
            //第三方支付交易号
            //创建时间
            $order->create_time = time();
            $i = 0;
            //保存 数据 之前 开启事务
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($order->validate()) {

                    $order->save(false);
                }
                //订单 商品 详情表
                foreach ($carts as $cart) {
                    $goods = Goods::findOne(['id' => $cart->goods_id]);
                    //判断 剩余库存
                    if ($goods->stock >= $cart->amount) {
                        //订单 商品 中间表
                        $orderGoods = new OrderGoods();
                        $orderGoods->order_id = $order->id;
                        $orderGoods->goods_id = $goods->id;
                        $orderGoods->goods_name = $goods->name;
                        $orderGoods->logo = $goods->logo;
                        $orderGoods->price = $goods->shop_price;
                        $orderGoods->amount = $cart->amount;
                        $orderGoods->total = $orderGoods->price * $cart->amount;
                        $orderGoods->save(false);
                        //库存
                        $goods->stock -= $cart->amount;
                        $goods->save(false);

                    } else {
                        throw new Exception('商品库存不足,请修改购物车');
                    }
                }
                //清空购物车
                foreach ($carts as $cart){
                    $cart->delete();
                }
                $transaction->commit();
            }catch (Exception $e) {
                //回滚
                $transaction->rollBack();
                $i ++ ;
            }
        }
        if($i > 0){
            //跳转到失败页面
            return $this->redirect(['order/index']);
        }else{
            return $this->render('add');
        }
    }
//订单详情
    public function actionDetail()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        } else {


            $member_id = \Yii::$app->user->id;
            $orders = Order::find()->where(['member_id' => $member_id])->all();
            foreach ($orders as $order) {
                $orderGoods = OrderGoods::find()->where(['order_id' => $order->id])->all();
                //商品图片 只保存3 张
                $i = 0;
                foreach ($orderGoods as $detail) {
                    $i++;
                    if ($i > 3) {
                        break;
                    }
                    //在 order 模型中 添加 logos 字段 保存logo (成员变量)
                    $order->logos[] = $detail->logo;
                }
            }
            return $this->render('order', ['orders' => $orders, 'html' => $this->getGoodsCategory()]);
        }
    }


}