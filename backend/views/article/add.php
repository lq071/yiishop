<?php
//$model -> status = 0; //默认选中
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model,'status')->radioList(['隐藏','正常']);
echo $form->field($model,'intro')->textarea();
echo $form->field($model2,'content')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();