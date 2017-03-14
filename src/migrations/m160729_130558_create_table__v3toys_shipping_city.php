<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */
use yii\db\Schema;
use yii\db\Migration;

class m160729_130558_create_table__v3toys_shipping_city extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%v3toys_shipping_city}}", true);
        if ($tableExist)
        {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%v3toys_shipping_city}}", [
            
            'id'                    => $this->primaryKey(),

            'name'                  => $this->string(255)->notNull(),

            'description'           => $this->text(),
            'price'                 => $this->decimal(18, 2)->comment('Стоимость'),

            'shipping_type'          => $this->string(20)->notNull()->defaultValue('COURIER'),
        ], $tableOptions);

        $this->createIndex('name', '{{%v3toys_shipping_city}}', 'name');
        $this->createIndex('shipping_type', '{{%v3toys_shipping_city}}', 'shipping_type');
        $this->createIndex('price', '{{%v3toys_shipping_city}}', 'price');

        $this->execute("ALTER TABLE {{%v3toys_shipping_city}} COMMENT = 'Города доставки';");
    }

    public function safeDown()
    {
        $this->dropTable("{{%v3toys_shipping_city}}");
    }
}