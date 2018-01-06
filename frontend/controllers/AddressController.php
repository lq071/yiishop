<?php
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Member;
use yii\web\Controller;

class AddressController extends Controller{
    public $enableCsrfValidation = false;

    public function actionIndex($id=''){
        $member_id = \Yii::$app->user->id;
        $rows = Address::find()->where(['member_id'=>$member_id])->all();
        //$rows = Address::find()->all();
        $model =Address::findOne(['id'=>$id]);
        if(!$model){
            $model = new Address();
        }

        return $this->render('address',['rows'=>$rows,'model'=>$model]);
    }

    public function actionAdd(){
        $request = \Yii::$app->request;
        $model =Address::findOne(['id'=>$request->post()['id']]);
        if(!$model){
            $model = new Address();
        }
        if($request->isPost){
            $model->load($request->post(),'');
           // var_dump($model);
           // var_dump($request->post());exit;
            if ($model->validate()) {
                $model->member_id = \Yii::$app->user->id;
                $model->save(false);
                return $this->redirect(['address/index']);
            } else {
                var_dump($model->getErrors());
            }
        }
    }
    public function actionDelete($id){
        $model =Address::findOne(['id'=>$id]);
        $model->delete();
        echo json_encode(1);
    }
}