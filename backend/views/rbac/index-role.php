<a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>">添加</a>
<table class="table">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row): ?>
    <tr data-id="<?=$row->name?>">
        <td><?=$row->name?></td>
        <td><?=$row->description?></td>
        <td><a class="btn btn-primary" href="<?=\yii\helpers\Url::to(['rbac/edit-role','name'=>$row->name])?>">修改</a>
            <a class="btn btn-danger" >删除</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['rbac/delete-role']).'?name=';
$str = <<<JS
       $('.table').on('click','.btn-danger',function(data){
           var tr = $(this).closest('tr');
           if(confirm('是否确定删除该记录？')){
            var name = tr.attr('data-id');
            $.getJSON("$url"+name ,function(){
                tr.fadeOut();
            });
           }        
            });
JS;

$this->registerJs($str);
