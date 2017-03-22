<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 25.07.2016
 */
namespace v3toys\skeeks\models;
use skeeks\cms\models\CmsStorageFile;
use skeeks\cms\shop\models\ShopProduct;
use skeeks\modules\cms\money\Money;
use yii\base\Model;

/**
 * Class V3toysOrder
 *
 * @property Money $money
 * @property Money $moneyTotal
 * @property ShopProduct $product
 * @property string $url
 * @property string $absoluteUrl
 * @property CmsStorageFile $image
 *
 * @package v3toys\skeeks\models
 */
class V3toysOrderBasket extends Model
{
    public $v3toys_product_id;
    public $price;
    public $quantity;
    public $name;
    public $product_id;


    /**
     * @return Money
     */
    public function getMoney()
    {
        return \Yii::$app->money->newMoney($this->price);
    }

    /**
     * @return Money
     */
    public function getMoneyTotal()
    {
        return $this->money->multiply($this->quantity);
    }

    /**
     * @return null|ShopProduct
     */
    public function getProduct()
    {
        return ShopProduct::findOne(['id' => $this->product_id]);
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        if ($this->product)
        {
            //Это предложение у него есть родительский элемент
            if ($parent = $this->product->cmsContentElement->parentContentElement)
            {
                return $parent->url;
            } else
            {
                return $this->product->cmsContentElement->url;
            }
        }

        return '';
    }

    /**
     * @return null|string
     */
    public function getAbsoluteUrl()
    {
        if ($this->product)
        {
            //Это предложение у него есть родительский элемент
            if ($parent = $this->product->cmsContentElement->parentContentElement)
            {
                return $parent->absoluteUrl;
            } else
            {
                return $this->product->cmsContentElement->absoluteUrl;
            }
        }

        return '';
    }

    /**
     * @return null|\skeeks\cms\models\CmsStorageFile
     */
    public function getImage()
    {
        if ($this->product)
        {
            //Это предложение у него есть родительский элемент
            if ($parent = $this->product->cmsContentElement->parentContentElement) {
                return $parent->image;
            } else {
                return $this->product->cmsContentElement->image;
            }
        }

        return null;
    }
}
