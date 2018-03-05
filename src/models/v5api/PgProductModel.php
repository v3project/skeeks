<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 17.03.2017
 */

namespace v3toys\skeeks\models\v5api;

use yii\base\Model;
use yii\db\ActiveRecord;

class PgProductModel extends ActiveRecord
{
    public static function tableName()
    {
        return 'product';
    }

    public static function getDb()
    {
        return \Yii::$app->dbV3project;
    }

}