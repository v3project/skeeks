<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */

namespace v3toys\skeeks\components;

use skeeks\cms\base\Component;
use skeeks\cms\helpers\StringHelper;
use skeeks\cms\models\CmsContent;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\shop\models\ShopPersonType;
use skeeks\widget\chosen\Chosen;
use skeeks\yii2\dadataSuggestApi\helpers\YandexGecodeHelper;
use v3toys\skeeks\helpers\ShippingHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @property ShopPersonType $shopPersonType
 *
 * @property [] $currentShippingData
 * @property [] $outletsData
 * @property ShippingHelper $currentShipping
 * @property bool $isCurrentShippingCache
 * @property array $notifyEmails
 *
 * Class V3toysSettings
 * @package v3toys\skeeks\components
 */
class V3toysSettings extends Component
{
    /**
     * @return array
     */
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => \Yii::t('v3toys/skeeks', 'Настройки v3toys'),
        ]);
    }

    /**
     * @var string
     */
    public $v3toysIdPropertyName = 'vendorId';

    /**
     * @var int
     */
    public $v3toysShopPersonTypeId;

    /**
     * @var string
     */
    public $affiliate_key;

    /**
     * @var array Контент свяазанный с v3project
     */
    public $content_ids = [];

    /**
     * @var string Статус заказа, когда он отправлен в Submitted
     */
    public $v3toysOrderStatusSubmitted;


    public $notify_emails;


    /**
     * @var float
     */
    public $pickup_discaunt_value = 0;
    /**
     * @var float
     */
    public $post_discaunt_value = 0;
    /**
     * @var float
     */
    public $courier_discaunt_value = 0;


    /**
     * @var float
     */
    public $price_discount_percent = 0;

    /**
     * С каких ip разрешен доступ к нашему апи
     * @var array
     */
    public $api_allow_ids = [
        //'31.148.138.49'
        '*'
    ];


    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['v3toysIdPropertyName', 'string'],
            ['content_ids', 'safe'],
            ['v3toysShopPersonTypeId', 'integer'],
            ['affiliate_key', 'string'],
            ['v3toysOrderStatusSubmitted', 'string'],
            ['notify_emails', 'string'],

            ['pickup_discaunt_value', 'number'],
            ['post_discaunt_value', 'number'],
            ['courier_discaunt_value', 'number'],

            ['price_discount_percent', 'number'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'v3toysIdPropertyName' => 'Название параметра у товаров — v3toys id',
            'content_ids' => 'Контент свяазанный с v3project',
            'v3toysShopPersonTypeId' => 'Профиль покупателя v3project',
            'affiliate_key' => 'Код аффилиата полученный в v3project',
            'v3toysOrderStatusSubmitted' => 'Статус заказа, когда он отправлен в Submitted',
            'notify_emails' => 'Email адреса уведомлений',
            'pickup_discaunt_value' => 'Скидка/Наценка на доставку самовывоза',
            'post_discaunt_value' => 'Скидка/Наценка на доставку почтой',
            'courier_discaunt_value' => 'Скидка/Наценка на доставку курьером',
            'price_discount_percent' => 'Скидка/Наценка на товары',
        ]);
    }

    public function attributeHints()
    {
        $link = urlencode(Url::base(true));
        $a = Html::a('http://www.seogadget.ru/ip?urls=' . $link, 'http://www.seogadget.ru/ip?urls=' . $link,
            ['target' => '_blank']);

        return ArrayHelper::merge(parent::attributeHints(), [
            'v3toysIdPropertyName' => 'Как называется свойство товаров, в котором храниться id товара из системы v3toys',
            'content_ids' => 'Обновление наличия и цен будет происходить у элементов этого выбранного контента',
            'v3toysShopPersonTypeId' => 'Необходимо настроить тип покупателя, и его свойства, для связи с данными v3toys [ <b>php yii v3toys/init/update-person-type</b> ]',
            'affiliate_key' => 'Ключ связан с ip адресом сайта, необходимо сообщить свой IP. Проверить IP можно тут: ' . $a,
            'notify_emails' => 'Укажите email адреса через запятую, на них будет приходить информация о новых заказах.',
            'pickup_discaunt_value' => 'Указывается в рублях.',
            'post_discaunt_value' => 'Указывается в рублях.',
            'courier_discaunt_value' => 'Указывается в рублях.',
            'price_discount_percent' => 'Указывается в процентах, эта сумма будет добавлена или вычтена из цены товара на v3toys',
        ]);
    }

    public function renderConfigForm(ActiveForm $form)
    {
        echo $form->fieldSet('Общие настройки');
        echo $form->field($this, 'affiliate_key');
        echo $form->field($this, 'v3toysIdPropertyName');
        echo $form->field($this, 'content_ids')->widget(Chosen::className(), [
            'multiple' => true,
            'items' => CmsContent::getDataForSelect(),
        ]);
        echo $form->field($this, 'notify_emails')->textarea(['rows' => 3]);

        /*echo $form->field($this, 'v3toysShopPersonTypeId')->widget(Chosen::className(),[
            'items' => ArrayHelper::map(ShopPersonType::find()->all(), 'id', 'name'),
        ]);*/
        /*echo $form->field($this, 'v3toysOrderStatusSubmitted')->widget(Chosen::className(),[
            'items' => ArrayHelper::map(ShopOrderStatus::find()->all(), 'code', 'name'),
        ]);*/
        echo $form->fieldSetEnd();

        echo $form->fieldSet('Настройки доставки');
        echo $form->field($this, 'pickup_discaunt_value');
        echo $form->field($this, 'post_discaunt_value');
        echo $form->field($this, 'courier_discaunt_value');
        echo $form->fieldSetEnd();

        echo $form->fieldSet('Скидка/Наценка на товары');
        echo $form->field($this, 'price_discount_percent');
        echo $form->fieldSetEnd();
    }

    /**
     * @return array
     */
    public function getNotifyEmails()
    {
        $emailsAll = [];
        if ($this->notify_emails) {
            $emails = explode(",", $this->notify_emails);

            foreach ($emails as $email) {
                $emailsAll[] = trim($email);
            }
        }

        return $emailsAll;
    }

    /**
     * @return ShopPersonType
     */
    public function getShopPersonType()
    {
        return ShopPersonType::findOne((int)$this->v3toysShopPersonTypeId);
    }


    /**
     * Новое api
     */


    private $_shipping = null;

    /**
     * @param array $geoobject
     * @param int $max_distance_from_outlet_to_geobject
     *
     * @return ShippingHelper
     */
    public function getShipping($geoobject = [], $max_distance_from_outlet_to_geobject = 50)
    {
        return new ShippingHelper([
            'apiData' => $this->getShippingData($geoobject, $max_distance_from_outlet_to_geobject)
        ]);
    }

    /**
     * Удобрый объект с информацией о текущей доставке.
     *
     * @return StringHelper
     */
    public function getCurrentShipping()
    {
        if ($this->_shipping !== null) {
            return $this->_shipping;
        }

        $this->_shipping = new ShippingHelper([
            'apiData' => $this->currentShippingData
        ]);

        return $this->_shipping;
    }

    /**
     * Данные по текущей доставке
     *
     * @return array
     */
    public function getCurrentShippingData()
    {
        return $this->getShippingData();
    }

    /**
     * @return bool
     */
    public function getIsCurrentShippingCache()
    {
        if (!\Yii::$app->dadataSuggest->address) {
            return false;
        }

        $geoobject = \Yii::$app->dadataSuggest->address->toArray();
        $cacheKey = md5(serialize($geoobject) . 50);
        return (bool)\Yii::$app->cache->get($cacheKey);
    }

    /**
     *
     * Получение данных по доставке + кэширование данных
     *
     * @param array $geoobject @see schema
     * @param int $max_distance_from_outlet_to_geobject Удаленность от геообъекта
     * @return array|mixed
     */
    public function getShippingData($geoobject = [], $max_distance_from_outlet_to_geobject = 50)
    {
        if (!$geoobject) {
            if (\Yii::$app->dadataSuggest->address) {
                $geoobject = \Yii::$app->dadataSuggest->address->toArray();
            }
        }
        if (!$geoobject) {
            return [];
        }

        $cacheKey = md5(serialize($geoobject) . $max_distance_from_outlet_to_geobject);

        if (!$data = \Yii::$app->cache->get($cacheKey)) {
            $response = \Yii::$app->v3projectApi->orderGetGuidingShippingData([
                'geobject' => $geoobject,
                'order' => [
                    'products' => [
                        [
                            'v3p_product_id' => 176837,
                            'quantity' => 1,
                            'realize_price' => 438,
                        ]
                    ],
                ],
                'filters' => [
                    'max_distance_from_outlet_to_geobject' => $max_distance_from_outlet_to_geobject
                ]
            ]);

            if ($response->isOk) {
                $data = $response->data;
                \Yii::$app->cache->set($cacheKey, $data, 3600 * 12);
            }
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getCurrentMinPickupPrice()
    {
        $minPrice = 0;

        if ($outlets = ArrayHelper::getValue($this->currentShippingData, 'pickup.outlets')) {
            foreach ($outlets as $outletData) {
                if ($outletPrice = ArrayHelper::getValue($outletData, 'guiding_realize_price')) {
                    $minPrice = $outletPrice;
                }
            }
        }

        return $minPrice;
    }







    /**
     * @param ShopCmsContentElement $shopCmsContentElement
     */
    /*public function getShippingDataByProduct(ShopCmsContentElement $shopCmsContentElement)
    {
        if (!\Yii::$app->dadataSuggest->address)
        {
            return [];
        }

        $response = \Yii::$app->v3projectApi->orderGetGuidingShippingData([
            'geobject' => \Yii::$app->dadataSuggest->address->data,
            'order' => [
                'products' => [
                    [
                        'v3p_product_id' => $shopCmsContentElement->relatedPropertiesModel->getAttribute($this->v3toysIdPropertyName),
                        'quantity' => 1,
                        'realize_price' => $shopCmsContentElement->shopProduct->baseProductPrice->money->getValue(),
                    ]
                ],
            ],
            'filters' => [
                'max_distance_from_outlet_to_geobject' => 20
            ]
        ]);

        if ($response->isOk)
        {
            print_r($response->data);
        }
    }*/

}
