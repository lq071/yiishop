<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\UploadedFile;

class GoodsController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    //列表
    public function actionIndex()
    {
        $query = Goods::find();
        $pages = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>2
        ]);
        $rows = $query->where(['status'=>1])->limit($pages->limit)->offset($pages->offset)->all();
        return $this->render('index',['rows'=>$rows,'pages'=>$pages]);
    }
    //ajax index
    public function actionSearch()
    {
        $request = \Yii::$app->request;
        $param = $request->post();
        $query = Goods::find();
        $pages = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>2
        ]);
        //var_dump($param); exit;
        $rows = $query->orderBy(['goods.sort'=>SORT_DESC])
            ->where(['status'=>1])
            ->andwhere([
                'and',
                ['like','sn',$param['sn']],
                ['like','name',$param['name']],
                ['like','market_price',$param['market_price']],
                ['like','shop_price',$param['shop_price']],
            ])
            /*->andFilterWhere(['like','sn',$param['sn']])
            ->andFilterWhere(['like','name',$param['name']])
            ->andFilterWhere(['like','market_price',$param['market_price']])
            ->andFilterWhere(['like','shop_price',$param['shop_price']])*/
            ->limit($pages->limit)->offset($pages->offset)
            ->all();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'details' => $rows
        ];
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new Goods();
        $model2 = new GoodsIntro();
        $brand = Brand::find()->where(['status'=>[0,1]])->all();
        if ($request->isPost) {
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate()){
                $model->create_time = time();
                $model->status = 1;
                //============= 计数 ==========
                $date = date('Ymd',time());
                $goodsDayCount =GoodsDayCount::findOne(['day'=>$date]);
                if(!$goodsDayCount){
                    $goodsDayCount = new GoodsDayCount();
                    $goodsDayCount ->day =$date;
                    $goodsDayCount->count =  1;
                    $model ->sn = $date.'00000'.$goodsDayCount->count;
                }else{
                    $goodsDayCount->count = $goodsDayCount->count + 1;
                    // $goodsDayCount->count  123  length < 6  00123
                    $temp = $goodsDayCount->count."";
                    $len = count($temp); //"123"
                    for($i =0;$i<6-$len;$i++){ // 3
                        $temp = "0".$temp; //"0123" //00123 //000123
                    }
                    $model ->sn = $date.$temp;
                }
                $model->save(false);

                $goodsDayCount->save(false);

                //var_dump($model2);exit;
                $model2->goods_id = $model->id;
                $model2->save(false);
                var_dump($model->getErrors());
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model,'model2'=>$model2,'brand'=>$brand]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model = Goods::findOne(['id'=>$id]);
        $model2 = GoodsIntro::findOne(['goods_id'=>$id]);
        //$brand = Brand::findOne(['id'=>$model->brand_id]);
        $brand = Brand::find()->where(['status'=>[0,1]])->all();
       //var_dump($model);
       // var_dump($model2);
        //var_dump($brand);exit;
        if ($request->isPost) {

            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate()){
                $model->save(false);
                $model2->save(false);
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['goods/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model,'model2'=>$model2,'brand'=>$brand]);
    }
    //删除
    public function actionDelete($id){
        $model = Goods::findOne(['id'=>$id]);
        $model->status = 0;
        //var_dump($model); exit;
        $model->save(false);
        echo json_encode(1);
    }
    //上传文件
    public function actionUpload(){
        $img = UploadedFile::getInstanceByName('file');
        $file = '/upload/' . uniqid() . '.' . $img->extension;
        if ($img->saveAs(\Yii::getAlias('@webroot') . $file)) {
            //================== 七牛云 =================
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="rWoVtEsy7XxYkUt0ZputvXtAunPTQxJiacYhb5nT";
            $secretKey = "iobjvc7w3THiggBPgvlXQmyXU1iPv3Kselam5Tlw";
            $bucket = "linqian";
            $domain = 'p1avc12q6.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            // echo "\n====> putFile result: \n";
            if ($err !== null) {
                echo json_encode('上传失败');
            } else {
                $url = "http://{$domain}/{$key}";
                return Json::encode(['url'=>$url]);
            }
        }
    }
    //富文本编辑器
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
    //相册列表
    public function actionGallery(){
        $gallery = GoodsGallery::find()->all();
        return $this->render('gallery',['gallery'=>$gallery]);
    }




}
