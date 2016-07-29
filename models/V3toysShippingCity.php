<?php

namespace v3toys\skeeks\models;

use skeeks\modules\cms\money\Money;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%v3toys_shipping_city}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property string $shipping_type
 *
 * @property string $shippingTypeName
 * @property string $fullName
 * @property Money $money
 *
 *
 * @property V3toysOrder[] $v3toysOrders
 */
class V3toysShippingCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3toys_shipping_city}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['shipping_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'price' => Yii::t('app', 'Стоимость'),
            'shipping_type' => Yii::t('app', 'Shipping Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3toysOrders()
    {
        return $this->hasMany(V3toysOrder::className(), ['shipping_city_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getShippingTypeName()
    {
        return (string) ArrayHelper::getValue(V3toysOrder::getShippingMethods(), $this->shipping_type);
    }

    /**
     * @param $shippingCode
     *
     * @return static[]
     */
    static public function getCitiesByShippingType($shippingCode)
    {
        $shippingCode = (string) $shippingCode;

        return static::find()->where(['shipping_type' => $shippingCode])->all();
    }

    /**
     * @return static[]
     */
    static public function getCitiesShippingCourier()
    {
        return static::getCitiesByShippingType(V3toysOrder::SHIPPING_METHOD_COURIER);
    }

    /**
     * @return static[]
     */
    static public function getCitiesShippingPickup()
    {
        return static::getCitiesByShippingType(V3toysOrder::SHIPPING_METHOD_PICKUP);
    }

    /**
     * @return \skeeks\modules\cms\money\Money
     */
    public function getMoney()
    {
        return \Yii::$app->money->newMoney($this->price, 'RUB');
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return "+" . \Yii::$app->money->convertAndFormat($this->money) . " | " . $this->name;
    }

    /**
     * Дата атрибуты для селекта
     * @param array $cities
     *
     * @return array
     */
    /*static public function getDataForJsonPrices($cities = [])
    {
        $result = [];
        /**
         * @var $city self
        foreach ($cities as $city)
        {
            $result[$city->id] = [
                'price' => $city->price,
                'priceFormated' => \Yii::$app->money->convertAndFormat($city->money),
            ];
        }

        return $result;
    }*/

}