<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
namespace v3toys\skeeks\models;

use skeeks\modules\cms\money\Money;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property int guidingRealizePriceAmount
 * @property Money guidingRealizeMoney
 *
 * Class V3toysOutletModel
 *
 * @package v3toys\skeeks\models
 */
class V3toysOutletModel extends Model
{
    public $v3p_outlet_id;
    public $v3p_provider_id;
    public $created_at;
    public $updated_at;
    public $guiding_realize_price;
    public $lat;
    public $lon;
    public $city;
    public $address;
    public $title;
    public $phone;
    public $metro_title;
    public $work_schedule;
    public $trip_description;
    public $description;
    public $same_group_key;
    public $same_group_ind;

    public $deliveryData = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['v3p_outlet_id'], 'integer'],
            [['v3p_provider_id'], 'integer'],
            [['created_at'], 'string'],
            [['updated_at'], 'string'],
            [['guiding_realize_price'], 'number'],
            [['lat'], 'number'],
            [['lon'], 'number'],
            [['city'], 'string'],
            [['address'], 'string'],
            [['title'], 'string'],
            [['phone'], 'string'],
            [['metro_title'], 'string'],
            [['work_schedule'], 'string'],
            [['trip_description'], 'string'],
            [['description'], 'string'],
            [['same_group_key'], 'string'],
            [['same_group_ind'], 'string'],
        ];
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'coords'
        ]);
    }

    /**
     * @return array
     */
    public function getCoords()
    {
        return [(float) $this->lat, (float) $this->lon];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * Получение всех оутлетов из апи
     *
     * @return array
     */
    static public function getAllDataFromApi()
    {
        $result = [];

        /*$query = (new \yii\db\Query())
                    ->from('apiv5.outlet')
                    ;

        $outlets = $query->all(\Yii::$app->dbV3project);
        print_r($outlets);die;
        */
        $response = \Yii::$app->v3projectApi->orderFindOutlets([
            'params' => [
                'format' => 'full'
            ]
        ]);

        if ($response->isOk)
        {
            $data = $response->data;
            $result = [];

            if ($data)
            {
                foreach ($data as $row)
                {
                    $result[ArrayHelper::getValue($row, 'v3p_outlet_id')] = $row;
                }
            }
        }

        return $result;
    }

    static public $models = null;

    /**
     * @return static[];
     */
    static public function getAll()
    {
        if (static::$models !== null)
        {
            return static::$models;
        }

        $key = "sx-v3toys-outlets";

        if (!$result = \Yii::$app->cache->get($key))
        {
            $result = static::getAllDataFromApi();

            if ($result)
            {
                \Yii::$app->cache->set($key, $result, 3600*5);
            }
        }

        foreach ($result as $key => $row)
        {
            static::$models[$key] = new static($row);
        }


        return static::$models;
    }

    /**
     * @param array $ids
     *
     * @return static[]
     */
    static public function getAllByIds($ids = [])
    {
        if (!$all = static::getAll())
        {
            return [];
        }

        $result = [];

        foreach ($ids as $id)
        {
            if (isset($all[$id]))
            {
                $result[$id] = $all[$id];
            }
        }

        return $result;
    }

    /**
     * Получение списка моделей оутлетов по ответу апи
     *
     * @param array $deliveryData
     *
     * @return static[]
     */
    static public function getAllByDeliveryData($deliveryData = [])
    {
        $result = [];

        if (!$deliveryData)
        {
            return [];
        }

        foreach ($deliveryData as $row)
        {
            if ($model = static::getById(ArrayHelper::getValue($row, 'v3p_outlet_id')))
            {
                $model->deliveryData            = $row;
                $result[$model->v3p_outlet_id]  = $model;
            }
        }

        return $result;
    }

    /**
     * @param $id
     *
     * @return static
     */
    static public function getById($id)
    {
        if (!$all = static::getAll())
        {
            return null;
        }

        if (isset($all[$id]))
        {
            return $all[$id];
        }

        return null;
    }

    /**
     * @return int
     */
    public function getGuidingRealizePriceAmount()
    {
        return (int) \yii\helpers\ArrayHelper::getValue($this->deliveryData, 'guiding_realize_price') + (int) \Yii::$app->v3toysSettings->pickup_discaunt_value;
    }

    /**
     * @return Money
     */
    public function getGuidingRealizeMoney()
    {
        return Money::fromString((string) $this->guidingRealizePriceAmount, "RUB");
    }

}