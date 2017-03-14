<?php

namespace v3toys\skeeks\models;

use Yii;

/**
 * This is the model class for table "{{%v3toys_order_status}}".
 *
 * @property integer $id
 * @property integer $v3toys_id
 * @property string $name
 * @property string $description
 * @property integer $priority
 * @property string $color
 */
class V3toysOrderStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3toys_order_status}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['v3toys_id', 'name'], 'required'],
            [['v3toys_id', 'priority'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('v3toys/skeeks', 'ID'),
            'v3toys_id' => Yii::t('v3toys/skeeks', 'V3toys ID'),
            'name' => Yii::t('v3toys/skeeks', 'Name'),
            'description' => Yii::t('v3toys/skeeks', 'Description'),
            'priority' => Yii::t('v3toys/skeeks', 'Priority'),
            'color' => Yii::t('v3toys/skeeks', 'Color'),
        ];
    }
}