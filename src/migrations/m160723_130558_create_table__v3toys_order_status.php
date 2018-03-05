<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160723_130558_create_table__v3toys_order_status extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%v3toys_order_status}}", true);
        if ($tableExist) {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%v3toys_order_status}}", [

            'id' => $this->primaryKey(),

            'v3toys_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),

            'description' => $this->text(),
            'priority' => $this->integer()->notNull()->defaultValue(100),

            'color' => $this->string(32),

        ], $tableOptions);

        $this->createIndex('v3toys_order_status__name', '{{%v3toys_order_status}}', 'name');
        $this->createIndex('v3toys_order_status__v3toys_id', '{{%v3toys_order_status}}', 'v3toys_id');
    }

    public function safeDown()
    {
        $this->dropTable("{{%v3toys_order_status}}");
    }
}