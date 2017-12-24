
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<table class="table">
    <tr>
        <th>图片</th>
        <th>操作</th>
    </tr>
  <?php foreach($rows as $row): ?>
    <tr data-id="<?=$row->id?>">
        <td><img src="<?=$row->path?>" width="200px"></td>
        <td>
            <a class="btn btn-danger" >删除</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <tbody>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[    'depends'=>\yii\web\JqueryAsset::className()
]);
$url = \yii\helpers\Url::to(['goods/upload']);
$url_add = \yii\helpers\Url::to(['goods-gallery/add']);
$url_delete = \yii\helpers\Url::to(['goods-gallery/delete']).'?id=';
$js = <<<JS
var uploader = WebUploader.create({
    // 选完文件后，是否自动上传。
    auto: true,
    // swf文件路径
    swf:'/js/Uploader.swf',
    // 文件接收服务端。
    server: "{$url}",
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
    $.post('{$url_add}',{'path':response.url,'goods_id':$goods_id},function(data){
        console.debug(data)
        var html = '';
        html += '<tr data-id="'+data.id+'">';
        html += '<td><img id="img" src="'+response.url+'" width="100px"> </td>';
        html += '<td><a class="btn btn-danger" >删除</a></td>';
        html += '<tr>'; 
         $(".table tbody").append(html);
    });
    $('#img').attr('src',response.url);*/
    //$('#brand-logo').val(response.url)
       // console.debug(response)
});
//删除
      $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var id = tr.attr('data-id');
            $.getJSON("{$url_delete}"+id ,function(){
                tr.fadeOut();
            });
           }        
            });
JS;
$this->registerJs($js);






