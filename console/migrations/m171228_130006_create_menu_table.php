<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171228_130006_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'label'=>$this->string(20)->notNull()->comment('菜单名称'),
            'url'=>$this->string(60)->notNull()->comment('路由'),
            'parent_id'=>$this->integer()->notNull()->comment('上级分类')
        ]);
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
