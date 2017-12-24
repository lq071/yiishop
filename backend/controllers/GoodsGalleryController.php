<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use yii\web\NotFoundHttpException;

class GoodsGalleryController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    //列表
    public function actionIndex($id)
    {
        $rows = GoodsGallery::find()->where(['goods_id'=> $id])->all();
        return $this->render('index',['rows'=>$rows,'goods_id'=>$id]);
    }
    //添加
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new GoodsGallery();
        $request->post();
        $model->path = $request->post()['path'];
        $model->goods_id = $request->post()['goods_id'];
        $model->save();
        $id = \Yii::$app->db->getLastInsertID();
        echo json_encode(['id'=>$id]);
    }

    //删除
    public function actionDelete($id){
        $model = GoodsGallery::findOne(['id'=>$id]);
        $model->delete();
        echo json_encode(1);
    }
}
