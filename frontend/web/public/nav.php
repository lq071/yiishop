<div class="topnav">
    <div class="topnav_bd w1210 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <?php if(Yii::$app->user->isGuest){?>
                    <li>您好，欢迎来到京西！
                        [<a href="<?=\yii\helpers\Url::to(['login/login'])?>">登录</a>] [<a href="<?=\yii\helpers\Url::to(['site/register'])?>">免费注册</a>]
                    </li>
                <?php }else{?>
                    <li>
                        <b><?=Yii::$app->user->identity->username?></b>
                        [<a href="<?=\yii\helpers\Url::to(['login/logout'])?>">退出</a>]
                    </li>
                <?php }?>
                <li class="line">|</li>
                <li><a href="<?=\yii\helpers\Url::to(['site/index'])?>">首页</a></li>
                <li class="line">|</li>
                <li><a href="<?=\yii\helpers\Url::to(['order/detail'])?>">我的订单</a></li>
                <li class="line">|</li>
                <li>客户服务</li>
            </ul>
        </div>
    </div>
</div>