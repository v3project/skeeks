<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 24.09.2016
 */

namespace v3toys\skeeks\helpers;

use skeeks\modules\cms\money\Money;
use v3toys\skeeks\models\V3toysOutletModel;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @property bool $isPickup
 * @property bool $isCourier
 * @property bool $isPost
 *
 * @property Money $pickupMinPrice
 * @property Money $courierMinPrice
 * @property Money $postMinPrice
 *
 * @property int $courierShippingDate
 * @property V3toysOutletModel[] $outlets
 *
 * Class ShippingHelper
 * @package v3toys\skeeks\helpers
 */
class ShippingHelper extends Component
{
    /**
     * @var array
     */
    public $apiData = [];

    /**
     * @return bool
     */
    public function getIsPickup()
    {
        return (bool)\yii\helpers\ArrayHelper::getValue($this->apiData, 'pickup');
    }

    /**
     * @return bool
     */
    public function getIsCourier()
    {
        return (bool)\yii\helpers\ArrayHelper::getValue($this->apiData, 'courier');
    }

    /**
     * @return bool
     */
    public function getIsPost()
    {
        return (bool)\yii\helpers\ArrayHelper::getValue($this->apiData, 'post');
    }

    /**
     * @return Money
     */
    public function getPostMinPrice()
    {
        $value = \yii\helpers\ArrayHelper::getValue($this->apiData, 'post.guiding_realize_price');
        $value = $value + (int)\Yii::$app->v3toysSettings->post_discaunt_value;
        return new \skeeks\cms\money\Money((string)$value, 'RUB');
    }

    /**
     * @return Money
     */
    public function getCourierMinPrice()
    {
        $value = \yii\helpers\ArrayHelper::getValue($this->apiData, 'courier.guiding_realize_price');
        $value = $value + (int)\Yii::$app->v3toysSettings->courier_discaunt_value;
        return  new \skeeks\cms\money\Money((string)$value, 'RUB');
    }

    /**
     * TODO: to release
     * @return Money
     */
    public function getPickupMinPrice()
    {
        $minPrice = 0;

        if ($outlets = ArrayHelper::getValue($this->apiData, 'pickup.outlets')) {

            foreach ($outlets as $outletData) {
                if ($outletPrice = ArrayHelper::getValue($outletData, 'guiding_realize_price')) {
                    if ($minPrice == 0 && $outletPrice != 0) {
                        $minPrice = $outletPrice;
                        continue;
                    }

                    if ($outletPrice < $minPrice) {
                        $minPrice = $outletPrice;
                    }
                }
            }
        }

        $minPrice = $minPrice + (int)\Yii::$app->v3toysSettings->pickup_discaunt_value;

        return  new \skeeks\cms\money\Money((string)$minPrice, 'RUB');
    }


    /**
     * @return V3toysOutletModel[]
     */
    public function getOutlets()
    {
        $outletsData = \yii\helpers\ArrayHelper::getValue($this->apiData, 'pickup.outlets');
        return \v3toys\skeeks\models\V3toysOutletModel::getAllByDeliveryData($outletsData);
    }

    /**
     * @return int
     */
    public function getCourierShippingDate()
    {
        $date = \yii\helpers\ArrayHelper::getValue($this->apiData, 'courier.guiding_to_shipping_date');
        return strtotime($date);
    }
}