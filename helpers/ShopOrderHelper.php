<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.07.2016
 */
namespace v3toys\skeeks\helpers;
use skeeks\cms\shop\models\ShopOrder;
use v3toys\skeeks\forms\CreateOrderForm;
use yii\base\Component;

/**
 * Class OrderHelper
 *
 * @package v3toys\skeeks\helpers
 */
class ShopOrderHelper extends Component
{
    /**
     * @var ShopOrder
     */
    public $shopOrder;

    public function getPhone()
    {
        $phone = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('phone');

        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace('+', '', $phone);
        $phone = trim($phone);

        return $phone;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $products = [];

        if ($this->shopOrder->shopBaskets)
        {
            foreach ($this->shopOrder->shopBaskets as $shopBasket)
            {
                $products[] = [
                    'product_id'    => (int) $shopBasket->product->cmsContentElement->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName),
                    'price'         => $shopBasket->price,
                    'quantity'      => $shopBasket->quantity,
                ];
            }
        }

        return $products;
    }

    public function getShippindData()
    {
        $result = [];

        if ($this->shopOrder->buyer->relatedPropertiesModel->getAttribute('shipping_method') == CreateOrderForm::SHIPPING_METHOD_COURIER)
        {
            $result['city'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('courier_city');
            $result['address'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('courier_address');

        } elseif ($this->shopOrder->buyer->relatedPropertiesModel->getAttribute('shipping_method') == CreateOrderForm::SHIPPING_METHOD_PICKUP)
        {
            $result['city'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('pickup_city');
            $result['point_id'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('pickup_point_id');

        } elseif ($this->shopOrder->buyer->relatedPropertiesModel->getAttribute('shipping_method') == CreateOrderForm::SHIPPING_METHOD_POST)
        {
            $result['index'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_index');
            $result['region'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_region');
            $result['area'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_area');
            $result['city'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_city');
            $result['address'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_address');
            $result['recipient'] = $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('post_recipient');
        }

        return $result;
    }
    /**
     * @return array
     */
    public function getApiRequestData()
    {
        return [
            'order_id'              => $this->shopOrder->id,  //имя клиента
            'fake'                  => 0,  //имя клиента
            'full_name'             => $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('name'),  //имя клиента
            'comment'               => $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('comment'),  //имя клиента
            'phone'                 => $this->getPhone(),  //имя клиента
            'email'                 => $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('email'),  //имя клиента
            'created_at'            => date("Y-m-d H:i:s", $this->shopOrder->created_at),  //имя клиента
            'products'              => $this->getProducts(),
            'shipping_method'       => $this->shopOrder->buyer->relatedPropertiesModel->getAttribute('shipping_method'),
            'shipping_cost' => 0,
            'shipping_data' => $this->getShippindData(),
        ];
    }

}
