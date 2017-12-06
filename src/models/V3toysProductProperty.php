<?php

namespace v3toys\skeeks\models;

use skeeks\cms\models\CmsContentElement;
use v3p\aff\models\V3pProduct;
use v3p\aff\models\V3pProductFeatureValue;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%v3toys_product_property}}".
 *
 * @property integer $id
 * @property integer $v3toys_id
 * @property integer $hero_id
 * @property integer $series_id
 * @property integer $sex
 * @property string $age_from
 * @property string $age_to
 * @property string $to_who
 * @property string $model
 * @property string $color
 * @property string $scale
 * @property string $number_of_parts
 * @property string $complect
 * @property string $players_number
 * @property string $allowable_weight
 * @property string $availability_batteries
 * @property string $batteries_type
 * @property string $game_time
 * @property string $charge_time
 * @property string $range
 * @property string $composition
 * @property string $number_pages
 * @property string $volume
 * @property string $size_of_box
 * @property string $size_of_toy
 * @property string $producing_country
 * @property integer $packing
 * @property string $extra
 * @property string $sku
 * @property string $stock_barcode
 * @property string $v3toys_brand_name
 * @property string $v3toys_title
 * @property string $v3toys_description
 * @property string $v3toys_video
 *
 * @property string $extraArray
 * @property string $ageString
 * @property string $sexString
 *
 *
 * @property CmsContentElement $cmsContentElement
 * @property V3pProduct $v3pProduct
 */
class V3toysProductProperty extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3toys_product_property}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getV3pProduct()
    {
        return $this->hasOne(V3pProduct::className(), ['id' => 'v3toys_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductFeatureValues()
    {
        return $this->hasMany(V3pProductFeatureValue::class, ['product_id' => 'v3toys_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['v3toys_id', 'hero_id', 'series_id', 'sex', 'packing'], 'integer'],
            [['age_from', 'age_to'], 'number'],
            [['extra'], 'string'],
            [
                [
                    'to_who',
                    'model',
                    'color',
                    'scale',
                    'number_of_parts',
                    'complect',
                    'players_number',
                    'allowable_weight',
                    'availability_batteries',
                    'batteries_type',
                    'game_time',
                    'charge_time',
                    'range',
                    'composition',
                    'number_pages',
                    'volume',
                    'size_of_box',
                    'size_of_toy',
                    'producing_country',
                    'sku',
                    'stock_barcode',
                    'v3toys_brand_name',
                    'v3toys_title',
                    'v3toys_description',
                    'v3toys_video'
                ],
                'string',
                'max' => 255
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'v3toys_id' => Yii::t('app', 'V3toys ID'),
            'hero_id' => Yii::t('app', 'Герой'),
            'series_id' => Yii::t('app', 'Серия'),
            'sex' => Yii::t('app', 'Пол'),
            'age_from' => Yii::t('app', 'Возраст от'),
            'age_to' => Yii::t('app', 'Возраст до'),
            'to_who' => Yii::t('app', 'Для кого'),
            'model' => Yii::t('app', 'Модель'),
            'color' => Yii::t('app', 'Цвет'),
            'scale' => Yii::t('app', 'Scale'),
            'number_of_parts' => Yii::t('app', 'Количество деталей'),
            'complect' => Yii::t('app', 'Комплект'),
            'players_number' => Yii::t('app', 'Количество игроков'),
            'allowable_weight' => Yii::t('app', 'Допустимый вес'),
            'availability_batteries' => Yii::t('app', 'Наличие батареек'),
            'batteries_type' => Yii::t('app', 'Тип батареек'),
            'game_time' => Yii::t('app', 'Время игры'),
            'charge_time' => Yii::t('app', 'Время зарядки'),
            'range' => Yii::t('app', 'Range'),
            'composition' => Yii::t('app', 'Материал'),
            'number_pages' => Yii::t('app', 'Количество страниц'),
            'volume' => Yii::t('app', 'Звук'),
            'size_of_box' => Yii::t('app', 'Размер упаковки'),
            'size_of_toy' => Yii::t('app', 'Размер игрушки'),
            'producing_country' => Yii::t('app', 'Страна производитель'),
            'packing' => Yii::t('app', 'Packing'),
            'extra' => Yii::t('app', 'Extra'),
            'sku' => Yii::t('app', 'Артикул'),
            'stock_barcode' => Yii::t('app', 'Штрих код'),
            'v3toys_brand_name' => Yii::t('app', 'Бренд'),
            'v3toys_title' => Yii::t('app', 'V3toys Title'),
            'v3toys_description' => Yii::t('app', 'V3toys Description'),
            'v3toys_video' => Yii::t('app', 'V3toys Video'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsContentElement()
    {
        return $this->hasOne(CmsContentElement::className(), ['id' => 'id']);
    }

    /**
     * @return array
     */
    public function toDetailViewArray()
    {
        $result = $this->toArray();

        unset($result['id']);
        unset($result['hero_id']);
        unset($result['age_from']);
        unset($result['age_to']);
        unset($result['extra']);
        unset($result['sku']);
        unset($result['packing']);
        unset($result['v3toys_brand_name']);
        unset($result['v3toys_title']);
        unset($result['v3toys_description']);
        unset($result['v3toys_video']);
        unset($result['v3toys_id']);

        $return = [];

        foreach ($result as $code => $value) {
            if ($value) {
                $return[$this->getAttributeLabel($code)] = $value;
            }
        }

        $return['Возраст'] = $this->ageString;
        $return['Пол'] = $this->sexString;

        if ($this->extraArray) {
            $return = ArrayHelper::merge($return, $this->extraArray);
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getExtraArray()
    {
        if ($this->extra) {
            return unserialize($this->extra);
        }

        return [];
    }

    /**
     * @return string
     */
    public function getAgeString()
    {
        $ageFrom = (float)$this->age_from;
        $ageTo = (float)$this->age_to;

        if ($this->age_from > 0 && $this->age_to > 0) {
            return "от {$ageFrom} " . \Yii::t(
                    'app',
                    '{n, plural, =0{-} =1{от года} one{от # года} few{от # лет} many{от # лет} other{от # лет}}',
                    ['n' => $ageTo]);
        } else {
            if ($this->age_from > 0) {
                return \Yii::t(
                    'app',
                    '{n, plural, =0{-} =1{от года} one{от # года} few{от # лет} many{от # лет} other{от # лет}}',
                    ['n' => $ageFrom]);
            } else {
                if ($this->age_to > 0) {
                    return \Yii::t(
                        'app',
                        '{n, plural, =0{-} =1{от года} one{от # года} few{от # лет} many{от # лет} other{от # лет}}',
                        ['n' => $ageTo]);
                }
            }
        }
    }


    /**
     * @return string
     */
    public function getSexString()
    {
        if ($this->sex == 20) {
            return 'жен.';
        } else {
            if ($this->sex == 30) {
                return 'жен. и муж.';
            }
        }

        return 'муж.';
    }


    /**
     * @var Controller
     */
    public $consoleController = null;

    public function stdout($message, $second = null)
    {
        if ($this->consoleController) {
            $this->consoleController->stdout($message, $second);
        }
    }


}