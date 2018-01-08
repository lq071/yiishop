<?php
namespace backend\controllers;


use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class BrandController extends Controller{
    public $enableCsrfValidation = false;
    //列表
    public function actionIndex(){
        $rows = Brand::find()->where(['status'=>[0,1]])->all();
        return $this->render('index',['rows'=>$rows]);
    }
    //添加
    public function actionAdd()
    {
        $request = \Yii::$app->request;
        $model = new Brand();
        $model->logo = '';
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['brand/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //修改
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $model =Brand::findOne(['id'=>$id]);
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()){
                $model->save(false);
                \Yii::$app->session->setFlash('success','更新成功');
                return $this->redirect(['brand/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    //删除
    public function actionDelete($id){
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
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
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ],
        ];
    }



}

