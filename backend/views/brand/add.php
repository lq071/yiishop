<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//=====================  图片上传 ========================
echo <<<HTML
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<img id="img" width="150px" src=" $model->logo"/>
HTML;

//==============================================
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model,'status')->radioList(['隐藏','正常']);
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
 \yii\bootstrap\ActiveForm::end();

 /**
  * @var $this \yii\web\View
  */
 $this->registerCssFile('@web/webuploader/webuploader.css');
 $this->registerJsFile('@web/webuploader/webuploader.js',[    'depends'=>\yii\web\JqueryAsset::className()
 ]);

$url = \yii\helpers\Url::to(['brand/upload']);
$js = <<<JS
var uploader = WebUploader.create({
    // 选完文件后，是否自动上传。
    auto: true,
    // swf文件路径
    swf:'/js/Uploader.swf',
    // 文件接收服务端。
    server: '{$url}',
    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});
uploader.on( 'uploadSuccess', function( file,response ) {
    //$( '#'+file.id ).addClass('upload-state-done');
    $('#img').attr('src',response.url);
    $('#brand-logo').val(response.url)
       // console.debug(response)
});
JS;
$this->registerJs($js);