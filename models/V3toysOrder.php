<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.07.2016
 */
namespace v3toys\skeeks\models;

use skeeks\cms\models\behaviors\HasJsonFieldsBehavior;
use skeeks\cms\models\CmsUser;
use skeeks\cms\models\forms\SignupForm;
use skeeks\cms\shop\models\ShopBuyer;
use skeeks\cms\shop\models\ShopFuser;
use skeeks\cms\shop\models\ShopOrder;
use skeeks\cms\shop\models\ShopPaySystem;
use skeeks\cms\validators\PhoneValidator;
use skeeks\modules\cms\money\Money;
use skeeks\yii2\dadataSuggestApi\helpers\SuggestAddressModel;
use v3toys\skeeks\V3toysModule;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use Yii;

/**
 * This is the model class for table "{{%v3toys_order}}".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property integer $shop_order_id
 * @property integer $v3toys_order_id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $comment
 * @property integer $is_call_me_15_min
 * @property string $products
 * @property string $discount
 * @property string $shipping_cost
 * @property string $shipping_method
 *
 * @property string $courier_address
 *
 * @property string $pickup_point_id
 *
 * @property string $post_index
 * @property string $post_address
 * @property string $post_recipient
 *
 * @property string $dadata_address
 *
 * @property integer $v3toys_status_id
 * @property string $key
 *
 * @property CmsUser $user
 *
 * @property V3toysOrderStatus $status
 *
 * @property array $productsForApi
 * @property string $phoneForApi
 * @property string $shippindDataForApi
 * @property string $deliveryName
 * @property string $deliveryFullName
 * @property string $paymentName
 *
 * @property V3toysOrderBasket[] $baskets
 *
 * @property SuggestAddressModel $dadataAddress
 *
 * @property Money $money
 * @property Money $moneyOriginal
 * @property Money $moneyDiscount
 * @property Money $moneyDelivery
 *
 * @property Money $moneyDeliveryFromApi
 *
 *
 * @see http://www.v3toys.ru/index.php?nid=api
 *      getOrderDataById    — 3.1.7 Метод getOrderDataById - получение данных заказа по номеру
 *      createOrder         — 3.1.1 Метод createOrder - создает заказ и возвращает его номер
 *
 * Class V3toysOrder
 * @package v3toys\skeeks\models
 */
class V3toysOrder extends \skeeks\cms\models\Core
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3toys_order}}';
    }

    const SHIPPING_METHOD_COURIER   = 'COURIER';
    const SHIPPING_METHOD_PICKUP    = 'PICKUP';
    const SHIPPING_METHOD_POST      = 'POST';

    /**
     * Доступные методы доставки
     *
     * @return array
     */
    static public function getShippingMethods()
    {
        return [
            static::SHIPPING_METHOD_COURIER => 'Доставка курьером',
            static::SHIPPING_METHOD_PICKUP  => 'Самовывоз',
            static::SHIPPING_METHOD_POST    => 'Доставка Почтой России',
        ];
    }


    public function init()
    {
        parent::init();

        $this->on(static::EVENT_AFTER_INSERT, [$this, '_afterCreateOrder']);
        $this->on(static::EVENT_BEFORE_INSERT, [$this, '_beforeCreateOrder']);
    }

    /**
     * Автоматическое добавление стоимости доставки при создании заказа
     * @param $e
     */
    public function _beforeCreateOrder($e)
    {}

    /**
     * После создания заказа, пробуем создать все что нужно в cms но это уже не обязательно, поэтому если что то, где то не сработает не столь важно
     *
     * @param $e
     */
    public function _afterCreateOrder($e)
    {
        try
        {
            //Если пользователя не было, пробуем создать
            if (!$this->user_id)
            {
                if (!$user = CmsUser::findOne(['email' => $this->email]))
                {
                    $user = $this->_createCmsUser();
                }

                //create user
                $this->user_id = $user->id;
                $this->save();
                $this->refresh();
            }

        } catch (\Exception $e)
        {
            \Yii::error($e->getMessage(), V3toysModule::className());
        }

    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            HasJsonFieldsBehavior::className() =>
            [
                'class'     => HasJsonFieldsBehavior::className(),
                'fields'    => ['products', 'dadata_address']
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
        [
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'user_id', 'v3toys_order_id', 'v3toys_status_id', 'is_call_me_15_min'], 'integer'],
            [['name', 'phone', 'email', 'shipping_method'], 'required'],
            [['comment', 'key'], 'string'],
            [['discount', 'shipping_cost'], 'number'],
            [['name', 'email', 'courier_city', 'courier_address', 'pickup_city', 'pickup_point_id', 'post_index', 'post_region', 'post_area', 'post_city', 'post_address', 'post_recipient'], 'string', 'max' => 255],
            [['dadata_address'], 'safe'],
            [['phone'], 'string', 'max' => 50],
            [['shipping_method'], 'string', 'max' => 20],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['updated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['user_id' => 'id']],
        ],

        [
            [['shipping_cost'], 'default', 'value' => function($model)
            {
                return $this->moneyDelivery->getValue();
            }],
            [['key'], 'default', 'value' => \Yii::$app->security->generateRandomString()],
            [['products'], 'safe'],
            [['products'], 'required'],
            [['email'], 'email'],
            [['phone'], PhoneValidator::className()],
            [['shipping_method'], 'in', 'range' => array_keys(static::getShippingMethods())],

            [['is_call_me_15_min'], 'boolean'],

            /*[['email'], 'unique',
                'targetClass'       => \Yii::$app->user->identityClass,
                'targetAttribute'   => 'email',
                'message'           => 'Отлично, вы уже зарегистрированны у нас. Авторизуйтесь на сайте.',
                'filter'            => function ($query) {
                    if ($this->user)
                    {
                        $query->andWhere(['!=', 'email', $this->user->email]);
                    }

                }
            ],*/

            [['courier_address'], 'required', 'when' => function ($model) {
                return $model->shipping_method == static::SHIPPING_METHOD_COURIER;
            }],

            [['pickup_point_id'], 'required', 'when' => function ($model) {
                return $model->shipping_method == static::SHIPPING_METHOD_PICKUP;
            }],

            [['post_index', 'post_address', 'post_recipient'], 'required', 'when' => function ($model) {
                return $model->shipping_method == static::SHIPPING_METHOD_POST;
            }]
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('v3toys/skeeks', 'ID'),
            'created_by' => Yii::t('v3toys/skeeks', 'Created By'),
            'updated_by' => Yii::t('v3toys/skeeks', 'Updated By'),
            'created_at' => Yii::t('v3toys/skeeks', 'Created At'),
            'updated_at' => Yii::t('v3toys/skeeks', 'Updated At'),
            'user_id' => Yii::t('v3toys/skeeks', 'User ID'),
            'v3toys_order_id' => Yii::t('v3toys/skeeks', 'V3toys Order ID'),
            'name' => Yii::t('v3toys/skeeks', 'Имя и фамилия'),
            'phone' => Yii::t('v3toys/skeeks', 'Телефон'),
            'email' => Yii::t('v3toys/skeeks', 'Email'),
            'comment' => Yii::t('v3toys/skeeks', 'Комментарий'),
            'is_call_me_15_min' => Yii::t('v3toys/skeeks', 'Готов принять звонок в течении 15 минут'),
            'products' => Yii::t('v3toys/skeeks', 'Товары'),
            'discount' => Yii::t('v3toys/skeeks', 'Скидка на заказ, указывается в рублях, без копеек'),
            'shipping_cost' => Yii::t('v3toys/skeeks', 'стоимость доставки'),
            'shipping_method' => Yii::t('v3toys/skeeks', 'Доставка'),
            'courier_address' => Yii::t('v3toys/skeeks', 'Адрес'),
            'pickup_point_id' => Yii::t('v3toys/skeeks', 'Пункт самовывоза'),
            'post_index' => Yii::t('v3toys/skeeks', 'Индекс'),
            'post_address' => Yii::t('v3toys/skeeks', 'Адрес'),
            'post_recipient' => Yii::t('v3toys/skeeks', 'Полное ФИО получателя'),
        ];;
    }


    /**
     * @return $this
     */
    public function saveToSession()
    {
        \Yii::$app->session->set("sx-v3toys-order", $this->toArray());
        return $this;
    }

    /**
     * Создание объекта для текущей ситуации
     *
     * @return static
     */
    static public function createCurrent()
    {
        $object = new static(['user_id' => \Yii::$app->user->id]);

        $object->setAttributes(
            (array) \Yii::$app->session->get("sx-v3toys-order")
        );

        if ($object->user)
        {
            if (!$object->email)
            {
                $object->email    = $object->user->email;
            }
            if (!$object->name)
            {
                $object->name    = $object->user->name;
            }
            if (!$object->phone)
            {
                $object->phone    = $object->user->phone;
            }
        }


        $products = [];

        if (\Yii::$app->shop->shopFuser->shopBaskets)
        {
            foreach (\Yii::$app->shop->shopFuser->shopBaskets as $shopBasket)
            {
                if ($shopBasket->product)
                {
                    $products[] = [
                        'v3toys_product_id'     => (int) $shopBasket->product->cmsContentElement->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName),
                        'price'                 => $shopBasket->price,
                        'quantity'              => $shopBasket->quantity,
                        'name'                  => (string) $shopBasket->product->cmsContentElement->name,
                        'product_id'            => (int) $shopBasket->product->cmsContentElement->id,
                    ];
                }

            }
        }

        //Наполнение заказа текущими продуктами из корзины
        $object->products   = $products;
        if (\Yii::$app->dadataSuggest->address)
        {
            $object->dadata_address  = \Yii::$app->dadataSuggest->address->toArray();
        }
        $object->discount   = \Yii::$app->shop->shopFuser->moneyDiscount->getValue();

        if ($object->dadataAddress)
        {
            //print_r(\Yii::$app->dadataSuggest->address->toArray());die;
            //$object->post_region = ArrayHelper::getValue(\Yii::$app->dadataSuggest->address->data, 'region');
            if (ArrayHelper::getValue($object->dadataAddress->data, 'city'))
            {
                //$object->post_city = ArrayHelper::getValue(\Yii::$app->dadataSuggest->address->data, 'city');
            } else if (ArrayHelper::getValue($object->dadataAddress->data, 'settlement'))
            {
                //$object->post_city = ArrayHelper::getValue(\Yii::$app->dadataSuggest->address->data, 'settlement');
            }
            //$object->post_area = ArrayHelper::getValue(\Yii::$app->dadataSuggest->address->data, 'area');
            $object->post_index = ArrayHelper::getValue($object->dadataAddress->data, 'postal_code');

            $object->post_address       = $object->dadataAddress->shortAddressString;
            $object->courier_address    = $object->dadataAddress->shortAddressString;
        }


        return $object;
    }

    private $_suggestAddress = null;

    /**
     * @return null|SuggestAddressModel
     */
    public function getDadataAddress()
    {
        if ($this->_suggestAddress !== null && $this->_suggestAddress instanceof SuggestAddressModel)
        {
            return $this->_suggestAddress;
        }

        if ($this->dadata_address)
        {
            $this->_suggestAddress = new SuggestAddressModel((array) $this->dadata_address);
        }

        return $this->_suggestAddress;
    }


    /**
     * @return null|\skeeks\cms\models\User
     * @throws Exception
     */
    protected function _createCmsUser()
    {
        $newUser                = new SignupForm();
        $newUser->scenario      = SignupForm::SCENARION_ONLYEMAIL;
        $newUser->email         = $this->email;

        if (!$user = $newUser->signup())
        {
            throw new Exception(\Yii::t('skeeks/shop/app', 'Do not create a user profile.'));
        }

        $user->name          = $this->name;
        $user->save();

        return $user;
    }


    /**
     * Телефон в правильном формате для апи
     *
     * @return string
     */
    public function getPhoneForApi()
    {
        $phone = $this->phone;

        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace('+', '', $phone);
        $phone = trim($phone);

        return (string) $phone;
    }

    /**
     * Формирование массива данных о доставке для отправки в апи
     * @return array
     */
    public function getShippindDataForApi()
    {
        $result = [];

        if ($this->shipping_method == static::SHIPPING_METHOD_COURIER)
        {
            $result['city']     = "Москва до МКАД";
            $result['address']  = $this->dadataAddress->unrestrictedValue;

        } elseif ($this->shipping_method == static::SHIPPING_METHOD_PICKUP)
        {
            //$result['city'] = "Москва";
            //$result['point_id'] = 1;
            $result['v3p_outlet_id'] = $this->pickup_point_id;

        } elseif ($this->shipping_method == static::SHIPPING_METHOD_POST)
        {
            $result['index'] = $this->post_index;
            $result['region'] = ArrayHelper::getValue($this->dadataAddress->data, 'region') ? ArrayHelper::getValue($this->dadataAddress->data, 'region') : "Не определено";
            $result['area'] = ArrayHelper::getValue($this->dadataAddress->data, 'area') ? ArrayHelper::getValue($this->dadataAddress->data, 'area') : "Не определено";
            $result['city'] = ArrayHelper::getValue($this->dadataAddress->data, 'city') ? ArrayHelper::getValue($this->dadataAddress->data, 'city') : "Не определено";;
            $result['address'] = $this->dadataAddress->unrestrictedValue;;
            $result['recipient'] = $this->post_recipient;
        }

        return $result;
    }

    public function getOldShippindDataForApi()
    {
        $result = [];
        if ($this->shipping_method == static::SHIPPING_METHOD_COURIER)
        {
            $result['city'] = $this->courier_city;
            $result['address'] = $this->courier_address;
        } elseif ($this->shipping_method == static::SHIPPING_METHOD_PICKUP)
        {
            $result['city'] = $this->pickup_city;
            $result['point_id'] = $this->pickup_point_id;
        } elseif ($this->shipping_method == static::SHIPPING_METHOD_POST)
        {
            $result['index'] = $this->post_index;
            $result['region'] = $this->post_region;
            $result['area'] = $this->post_area;
            $result['city'] = $this->post_city;
            $result['address'] = $this->post_address;
            $result['recipient'] = $this->post_recipient;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getPaymentName()
    {
        if ($this->shipping_method == static::SHIPPING_METHOD_POST)
        {
            return 'Наложенный платеж';
        } else
        {
            return 'Наличные при получении заказа';
        }
    }

    /**
     * @return string
     */
    public function getDeliveryFullName()
    {
        if ($this->dadataAddress)
        {
            if ($this->shipping_method == static::SHIPPING_METHOD_PICKUP)
            {
                if ($model = V3toysOutletModel::getById($this->pickup_point_id))
                {
                    return $this->deliveryName . " (" . $model->city . ", " . $model->address . ")";
                }
            } else
            {
                return $this->deliveryName . " (" . $this->dadataAddress->unrestrictedValue . ")";
            }
        } else
        {
            //Старое апи
            $data = $this->getOldShippindDataForApi();
            ArrayHelper::remove($data, 'point_id');

            return $this->deliveryName . " (" . implode(', ', $data) . ")";
        }
    }

    /**
     * @return array
     */
    public function getProductsForApi()
    {
        $result = [];

        if ($this->products)
        {
            foreach ((array) $this->products as $productdata)
            {
                $result[] = [
                    'product_id'    => ArrayHelper::getValue($productdata, 'v3toys_product_id'),
                    'price'         => ArrayHelper::getValue($productdata, 'price'),
                    'quantity'      => ArrayHelper::getValue($productdata, 'quantity'),
                ];
            }
        }

        return $result;
    }
    /**
     * @return array
     */
    public function getApiRequestData()
    {
        return [
            'order_id'              => $this->id,
            'fake'                  => 0,
            'full_name'             => $this->name,
            'comment'               => $this->deliveryFullName . "\n" . ($this->comment ? "От клиента: " . $this->comment : ""),
            'phone'                 => $this->phoneForApi,
            'email'                 => $this->email,
            'created_at'            => date("Y-m-d H:i:s", $this->created_at),
            'products'              => $this->productsForApi,
            'shipping_method'       => $this->shipping_method,
            'shipping_cost'         => $this->moneyDelivery->getValue(),
            'shipping_data'         => $this->shippindDataForApi,
        ];
    }


    protected $_baskets = null;

    /**
     * @return V3toysOrderBasket[]
     */
    public function getBaskets()
    {
        if ($this->_baskets !== null)
        {
            return $this->_baskets;
        }

        $result = [];

        if ($this->products)
        {
            foreach ($this->products as $productData)
            {
                $obj = new V3toysOrderBasket();
                $obj->setAttributes($productData, false);
                $result[] = $obj;
            }
        }

        $this->_baskets = $result;

        return $this->_baskets;
    }

    /**
     * @return mixed
     */
    public function getDeliveryName()
    {
        return (string) ArrayHelper::getValue(static::getShippingMethods(), $this->shipping_method);
    }


    /**
     *
     * Итоговая стоимость корзины с учетом скидок, то что будет платить человек
     *
     * @return Money
     */
    public function getMoney()
    {
        $money = $this->moneyOriginal;

        if ($this->moneyDelivery)
        {
            $money = $money->add($this->moneyDelivery);
        }

        return $money;
    }

    private $_moneyDeliveryFromApi = null;
    /**
     * @return Money
     */
    public function getMoneyDeliveryFromApi()
    {
        if ($this->_moneyDeliveryFromApi !== null)
        {
            return $this->_moneyDeliveryFromApi;
        }

        //Данные по доставке из апи
        $shipping = \Yii::$app->v3toysSettings->getShipping($this->dadata_address);

        if ($this->shipping_method == static::SHIPPING_METHOD_COURIER)
        {
            if ($shipping->isCourier)
            {
                $this->_moneyDeliveryFromApi = $shipping->courierMinPrice;
            }

        } elseif ($this->shipping_method == static::SHIPPING_METHOD_PICKUP)
        {

            if ($shipping->isPickup)
            {
                if ($shipping->outlets && isset($shipping->outlets[$this->pickup_point_id]))
                {
                    /**
                     * @var $outlet V3toysOutletModel
                     */
                    $outlet = $shipping->outlets[$this->pickup_point_id];
                    $value = (int) ArrayHelper::getValue($outlet->deliveryData, 'guiding_realize_price');
                    $value = $value + (int) \Yii::$app->v3toysSettings->pickup_discaunt_value;
                    $this->_moneyDeliveryFromApi = Money::fromString((string) $value, "RUB");
                }
            }

        } elseif ($this->shipping_method == static::SHIPPING_METHOD_POST)
        {
            if ($shipping->isPost)
            {
                $this->_moneyDeliveryFromApi = $shipping->postMinPrice;
            }
        }

        if ($this->_moneyDeliveryFromApi === null)
        {
            $this->_moneyDeliveryFromApi = Money::fromString((string) 0, "RUB");
        }
        return $this->_moneyDeliveryFromApi;
    }

    /**
     * @return \skeeks\modules\cms\money\Money
     */
    public function getMoneyDelivery()
    {
        if ($this->isNewRecord)
        {
            return $this->moneyDeliveryFromApi;
        }

        return Money::fromString((string) $this->shipping_cost, "RUB");;
    }

    /**
     * @return \skeeks\modules\cms\money\Money
     */
    public function getMoneyDiscount()
    {
        return \Yii::$app->money->newMoney($this->discount);
    }

    /**
     *
     * Цена всех товаров
     *
     * @return Money
     */
    public function getMoneyOriginal()
    {
        $money = \Yii::$app->money->newMoney();

        foreach ($this->baskets as $basket)
        {
            $money = $money->add($basket->moneyTotal);
        }

        return $money;
    }




    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(V3toysOrderStatus::className(), ['v3toys_id' => 'v3toys_status_id']);
    }

}
