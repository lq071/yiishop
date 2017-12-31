<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '哒哒哒',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems=[];
/*    $menuItems = [
     [
            'label'=>'品牌管理',
            'items'=>[
                ['label' => '品牌列表', 'url' =>['/brand/index']],
               '<li class="divider"></li>',
                ['label' => '添加品牌', 'url' => ['/brand/add']],
            ]
        ],
     [
            'label'=>'商品管理',
            'items'=>[
                ['label' => '商品列表', 'url' =>['/goods/index']],
                '<li class="divider"></li>',
                ['label' => '添加商品', 'url' => ['/goods/add']],
                '<li class="divider"></li>',
                ['label' => '商品分类列表', 'url' => ['/goods-category/index']],
                '<li class="divider"></li>',
                ['label' => '添加商品分类', 'url' => ['/goods-category/add']],
            ]
        ],
        [
            'label'=>'文章管理',
            'items'=>[
                ['label' => '文章列表', 'url' =>['/article/index']],
                '<li class="divider"></li>',
                ['label' => '添加文章', 'url' => ['/article/add']],
            ]
        ],
        [
            'label'=>'RBAC',
            'items'=>[
                ['label' => '权限列表', 'url' =>['/rbac/index']],
                ['label' => '添加权限', 'url' => ['/rbac/add-p']],
                '<li class="divider"></li>',
                ['label' => '角色列表', 'url' => ['/rbac/index-role']],
                ['label' => '添加角色', 'url' => ['/rbac/add-role']],
            ]
        ],
        [
            'label'=>'用户管理',
            'items'=>[
                ['label' => '用户列表', 'url' =>['/user/index']],
                '<li class="divider"></li>',
                ['label' => '添加用户', 'url' => ['/user/add']],
            ]
        ],
        [
            'label'=>'个人中心',
            'items'=>[
                ['label' => '修改密码', 'url' =>['/user/edit-pwd']],
                '<li class="divider"></li>',
            ]
        ],
    ];*/
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/login/login']];
    } else {
        $menuItems=Yii::$app->user->identity->menus;
        $menuItems[] = '<li>'
            . Html::beginForm(['/login/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
