<?php
$model->status = 10;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'old_password')->passwordInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'password_hash2')->passwordInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status')->radioList([0=>'禁用',10=>'启用']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();
