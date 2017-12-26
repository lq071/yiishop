<a href="<?=\yii\helpers\Url::to(['user/add'])?>" class="btn btn-primary">添加</a>
<table class="table">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>状态</th>
        <th>email</th>
        <th>添加时间</th>
        <th>更新时间</th>
        <th>最后登录时间</th>
        <th>最后登录ip</th>
        <th>操作</th>
    </tr>
    <?php foreach($rows as $row):?>
    <tr data-id="<?=$row->id?>">
        <td><?=$row->id?></td>
        <td><?=$row->username?></td>
        <td><?=$row->status?></td>
        <td><?=$row->email?></td>
        <td><?=date('Y-m-d',$row->created_at)?></td>
        <td><?=date('Y-m-d',$row->updated_at)?></td>
        <td><?=date('Y-m-d',$row->last_login_time)?></td>
        <td><?=$row->last_login_ip?></td>
        <td><a href="<?=\yii\helpers\Url::to(['user/edit','id'=>$row->id])?>" class="btn btn-primary">修改</a>
            <a class="btn btn-danger" >删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['user/delete']).'?id=';
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
