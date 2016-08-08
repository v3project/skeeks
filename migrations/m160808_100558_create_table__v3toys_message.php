<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */
use yii\db\Schema;
use yii\db\Migration;

class m160808_100558_create_table__v3toys_message extends Migration
{
    public function safeUp()
    {
        $tableExist = $this->db->getTableSchema("{{%v3toys_message}}", true);
        if ($tableExist)
        {
            return true;
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable("{{%v3toys_message}}", [
            'id'                    => $this->primaryKey(),

            'created_by'            => $this->integer(),
            'updated_by'            => $this->integer(),

            'created_at'            => $this->integer(),
            'updated_at'            => $this->integer(),

            'user_id'               => $this->integer(),

            'full_name'             => $this->string(255)->notNull()->comment('Имя клиента'),
            'phone'                 => $this->string(50)->comment('Телефон'),
            'email'                 => $this->string(255)->comment('Email'),
            'comment'               => $this->text()->comment('Комментарий'),

            'products'              => $this->text()->comment('Товары'),

        ], $tableOptions);

        $this->createIndex('updated_by', '{{%v3toys_message}}', 'updated_by');
        $this->createIndex('created_by', '{{%v3toys_message}}', 'created_by');
        $this->createIndex('created_at', '{{%v3toys_message}}', 'created_at');
        $this->createIndex('updated_at', '{{%v3toys_message}}', 'updated_at');

        $this->createIndex('full_name', '{{%v3toys_message}}', 'full_name');
        $this->createIndex('phone', '{{%v3toys_message}}', 'phone');
        $this->createIndex('email', '{{%v3toys_message}}', 'email');

        $this->execute("ALTER TABLE {{%v3toys_message}} COMMENT = 'Быстрые заказы v3toys';");

        $this->addForeignKey(
            'v3toys_message__created_by', "{{%v3toys_message}}",
            'created_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'v3toys_message__updated_by', "{{%v3toys_message}}",
            'updated_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            'v3toys_message__user_id', "{{%v3toys_message}}",
            'user_id', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("v3toys_message__created_by", "{{%v3toys_message}}");
        $this->dropForeignKey("v3toys_message__updated_by", "{{%v3toys_message}}");
        $this->dropForeignKey("v3toys_message__user_id", "{{%v3toys_message}}");

        $this->dropTable("{{%v3toys_message}}");
    }
}