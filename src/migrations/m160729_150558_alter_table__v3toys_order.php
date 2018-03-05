<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.07.2016
 */

use yii\db\Schema;
use yii\db\Migration;

class m160729_150558_alter_table__v3toys_order extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%v3toys_order}}', 'courier_city_id', $this->integer());
        $this->addColumn('{{%v3toys_order}}', 'pickup_city_id', $this->integer());

        $this->addForeignKey(
            'v3toys_order__courier_city_id', "{{%v3toys_order}}",
            'courier_city_id', '{{%v3toys_shipping_city}}', 'id', 'SET NULL', 'SET NULL'
        );
        $this->addForeignKey(
            'v3toys_order__pickup_city_id', "{{%v3toys_order}}",
            'pickup_city_id', '{{%v3toys_shipping_city}}', 'id', 'SET NULL', 'SET NULL'
        );
    }

    public function safeDown()
    {
        return true;
    }
}