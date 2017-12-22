<?php
$model -> parent_id = 0;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
//echo $form->field($model,'parent_id')->dropDownList([]);
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();