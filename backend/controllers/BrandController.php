<?php
namespace backend\controllers;


use backend\models\Brand;
use yii\web\Controller;
use yii\web\UploadedFile;

class BrandController extends Controller{
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
        if ($request->isPost) {
            $model->load($request->post());
            //加载文件
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');

            if ($model->validate()) {
                //处理上传文件
                $file = '/upload/' . uniqid() . '.' . $model->imgFile->extension;
                if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) { //相当于 move_uploaded_file
                    //文件上传成功 保存路径
                    $model->logo = $file;
                }
                $model->save(false);
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
            //加载文件
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');

            if ($model->validate()){
                //处理上传文件
                if($model->imgFile){
                    $file = '/upload/' . uniqid() . '.' . $model->imgFile->extension;
                    if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) { //相当于 move_uploaded_file
                        //文件上传成功 保存路径
                        $model->logo = $file;
                    }
                }
                $model->save(false);
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



}

