<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160723_100558_create_table__v3toys_order extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%v3toys_order}}", true);
        if ($tableExist) {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%v3toys_order}}", [
            'id' => $this->primaryKey(),

            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),

            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

            'user_id' => $this->integer(),

            'shop_order_id' => $this->integer(),
            'v3toys_order_id' => $this->integer(),

            'name' => $this->string(255)->notNull()->comment('Имя и фамилия'),
            'phone' => $this->string(50)->notNull()->comment('Телефон'),
            'email' => $this->string(255)->notNull()->comment('Email'),
            'comment' => $this->text()->comment('Комментарий'),

            'is_call_me_15_min' => $this->integer(1)->notNull()->defaultValue(1)->comment('Готов принять звонок в течении 15 минут'),

            'products' => $this->text()->comment('Товары'),

            'discount' => $this->decimal(18, 2)->comment('Скидка на заказ, указывается в рублях, без копеек'),
            'shipping_cost' => $this->decimal(18, 2)->comment('стоимость доставки'),

            'shipping_method' => $this->string(20)->notNull()->comment('Доставка'),

            'courier_city' => $this->string(255)->comment('Город'),
            'courier_address' => $this->string(255)->comment('Адрес'),

            'pickup_city' => $this->string(255)->comment('Город'),
            'pickup_point_id' => $this->string(255)->defaultValue(1)->comment('Пункт самовывоза'),

            'post_index' => $this->string(255)->comment('Индекс'),
            'post_region' => $this->string(255)->comment('Регион'),
            'post_area' => $this->string(255)->comment('Область'),
            'post_city' => $this->string(255)->comment('Город'),
            'post_address' => $this->string(255)->comment('Адрес'),
            'post_recipient' => $this->string(255)->comment('Полное ФИО получателя'),

        ], $tableOptions);

        $this->createIndex('updated_by', '{{%v3toys_order}}', 'updated_by');
        $this->createIndex('created_by', '{{%v3toys_order}}', 'created_by');
        $this->createIndex('created_at', '{{%v3toys_order}}', 'created_at');
        $this->createIndex('updated_at', '{{%v3toys_order}}', 'updated_at');

        $this->createIndex('name', '{{%v3toys_order}}', 'name');
        $this->createIndex('phone', '{{%v3toys_order}}', 'phone');
        $this->createIndex('email', '{{%v3toys_order}}', 'email');
        $this->createIndex('is_call_me_15_min', '{{%v3toys_order}}', 'is_call_me_15_min');
        $this->createIndex('shipping_method', '{{%v3toys_order}}', 'shipping_method');
        $this->createIndex('shop_order_id', '{{%v3toys_order}}', 'shop_order_id');
        $this->createIndex('v3toys_order_id', '{{%v3toys_order}}', 'v3toys_order_id');

        $this->execute("ALTER TABLE {{%v3toys_order}} COMMENT = 'Заказы v3toys';");

        $this->addForeignKey(
            'v3toys_order__created_by', "{{%v3toys_order}}",
            'created_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'v3toys_order__updated_by', "{{%v3toys_order}}",
            'updated_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'v3toys_order__user_id', "{{%v3toys_order}}",
            'user_id', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'v3toys_order__shop_order_id', "{{%v3toys_order}}",
            'shop_order_id', '{{%shop_order}}', 'id', 'SET NULL', 'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("v3toys_order__created_by", "{{%v3toys_order}}");
        $this->dropForeignKey("v3toys_order__updated_by", "{{%v3toys_order}}");
        $this->dropForeignKey("v3toys_order__shop_order_id", "{{%v3toys_order}}");

        $this->dropTable("{{%v3toys_order}}");
    }
}