
<a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['menu/add'])?>">添加</a>
<table class="table">
    <tr>
        <th>菜单名称</th>
        <th>路由</th>
        <th>上级菜单</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row): ?>
    <tr data-id="<?=$row->id?>">
        <td><?=$row->label?></td>
        <td><?=$row->url?></td>
        <td><?=$row->parent_id?></td>
        <td><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['menu/edit','id'=>$row->id])?>">修改</a>
            <a class="btn btn-danger" >删除</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['menu/delete']).'?id=';
$str = <<<JS
       $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var id = tr.attr('data-id');
            $.getJSON("$url"+id ,function(){
                tr.fadeOut();
            });
           }        
            });
JS;

$this->registerJs($str);
