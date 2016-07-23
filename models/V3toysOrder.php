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
 * @property string $courier_city
 * @property string $courier_address
 * @property string $pickup_city
 * @property string $pickup_point_id
 * @property string $post_index
 * @property string $post_region
 * @property string $post_area
 * @property string $post_city
 * @property string $post_address
 * @property string $post_recipient
 * @property integer $v3toys_status_id
 * @property string $key
 *
 * @property ShopOrder $shopOrder
 * @property CmsUser $user
 *
 *
 * @property V3toysOrderStatus $status
 *
 *
 *
 * Форма используется для создания заказа
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
            static::SHIPPING_METHOD_COURIER => 'доставка курьером',
            static::SHIPPING_METHOD_PICKUP  => 'самовывоз',
            static::SHIPPING_METHOD_POST    => 'доставка Почтой России',
        ];
    }


    public function init()
    {
        parent::init();

        $this->on(static::EVENT_AFTER_INSERT, [$this, '_afterCreateOrder']);
    }


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
                //create user
                $newUser = $this->_createCmsUser();
                $this->user_id = $newUser->id;
                $this->save();

                //И сразу же авторизовать
                \Yii::$app->user->login($this->user, 0);
            }


            $this->initShopBuyer();
            $shopBuyer = $this->getShopBuyer();

            $fuser = \Yii::$app->shop->shopFuser;

            $fuser->user_id  = $this->user->id;
            $fuser->scenario = ShopFuser::SCENARIO_CREATE_ORDER;
            $fuser->buyer_id = $shopBuyer->id;
            $fuser->person_type_id = $shopBuyer->shop_person_type_id;

            //TODO:: рефакторинг
            if ($paySystem = ShopPaySystem::find()->orderBy(['priority' => SORT_ASC])->one())
            {
                $fuser->pay_system_id = $paySystem->id;
            }


            if ($fuser->validate())
            {
                $order = ShopOrder::createOrderByFuser($fuser);

                $this->shop_order_id = $order->id;
                $this->save();

                if (!$order->isNewRecord)
                {
                    return $order;

                } else
                {
                    throw new Exception(\Yii::t('skeeks/shop/app', 'Incorrect data of the new order').": " . array_shift($order->getFirstErrors()));
                }
            } else
            {
                throw new Exception(Json::encode($fuser->errors));
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
                'fields'    => ['products']
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
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'user_id', 'shop_order_id', 'v3toys_order_id', 'v3toys_status_id', 'is_call_me_15_min'], 'integer'],
            [['name', 'phone', 'email', 'shipping_method'], 'required'],
            [['comment', 'key'], 'string'],
            [['discount', 'shipping_cost'], 'number'],
            [['name', 'email', 'courier_city', 'courier_address', 'pickup_city', 'pickup_point_id', 'post_index', 'post_region', 'post_area', 'post_city', 'post_address', 'post_recipient'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['shipping_method'], 'string', 'max' => 20],
            [['shop_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShopOrder::className(), 'targetAttribute' => ['shop_order_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['updated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['user_id' => 'id']],
        ],

        [
            [['key'], 'default', 'value' => \Yii::$app->security->generateRandomString()],
            [['products'], 'safe'],
            [['products'], 'required'],
            [['email'], 'email'],
            [['phone'], PhoneValidator::className()],
            [['shipping_method'], 'in', 'range' => array_keys(static::getShippingMethods())],

            [['is_call_me_15_min'], 'boolean'],

            [['email'], 'unique',
                'targetClass'       => \Yii::$app->user->identityClass,
                'targetAttribute'   => 'email',
                'message'           => 'Отлично, вы уже зарегистрированны у нас. Авторизуйтесь на сайте.',
                'filter'            => function ($query) {
                    if ($this->user)
                    {
                        $query->andWhere(['!=', 'email', $this->user->email]);
                    }

                }
                /*'when' => function ($model) {
                    return $model->shipping_method == static::SHIPPING_METHOD_COURIER;
                }*/
            ],

            [['courier_city', 'courier_address'], 'required', 'when' => function ($model) {
                return $model->shipping_method == static::SHIPPING_METHOD_COURIER;
            }],

            [['pickup_city', 'pickup_point_id'], 'required', 'when' => function ($model) {
                return $model->shipping_method == static::SHIPPING_METHOD_PICKUP;
            }],

            [['post_index', 'post_region', 'post_city', 'post_address', 'post_recipient'], 'required', 'when' => function ($model) {
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
            'shop_order_id' => Yii::t('v3toys/skeeks', 'Shop Order ID'),
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
            'courier_city' => Yii::t('v3toys/skeeks', 'Город'),
            'courier_address' => Yii::t('v3toys/skeeks', 'Адрес'),
            'pickup_city' => Yii::t('v3toys/skeeks', 'Город'),
            'pickup_point_id' => Yii::t('v3toys/skeeks', 'Пункт самовывоза'),
            'post_index' => Yii::t('v3toys/skeeks', 'Индекс'),
            'post_region' => Yii::t('v3toys/skeeks', 'Регион'),
            'post_area' => Yii::t('v3toys/skeeks', 'Область'),
            'post_city' => Yii::t('v3toys/skeeks', 'Город'),
            'post_address' => Yii::t('v3toys/skeeks', 'Адрес'),
            'post_recipient' => Yii::t('v3toys/skeeks', 'Полное ФИО получателя'),
        ];;
    }


    /**
     * Создание объекта для текущей ситуации
     *
     * @return static
     */
    static public function createCurrent()
    {
        $object = new static(['user_id' => \Yii::$app->user->id]);

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
                $products[] = [
                    'product_id'    => (int) $shopBasket->product->cmsContentElement->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName),
                    'price'         => $shopBasket->price,
                    'quantity'      => $shopBasket->quantity,
                ];
            }
        }

        $object->products = $products;

        return $object;
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
     *
     * Заполнение профиля пользователя данными
     *
     * @return $this
     * @throws Exception
     * @throws \skeeks\cms\relatedProperties\models\InvalidParamException
     */
    public function initShopBuyer()
    {
        $shopBuyer = $this->getShopBuyer();

        $properties = $shopBuyer->relatedPropertiesModel;
        foreach ($properties->toArray() as $code => $value)
        {
            if ($this->offsetExists($code))
            {
                $properties->setAttribute($code, $this->{$code});
            }
        }

        if (!$properties->save())
        {
            throw new Exception('Не сохранены данные пользователя');
        }

        return $this;
    }

    /**
     *
     * Получение профиля покупателя, если у пользователя еще нет профиля покупателя, то необходимо создать согласно настройкам.
     *
     * @return array|null|ShopBuyer
     * @throws Exception
     * @throws \skeeks\cms\shop\models\InvalidParamException
     */
    public function getShopBuyer()
    {
        //Уже выбран покупатель

        //Тип покупателя из настроек магазина
        if (!$shopPersonType = \Yii::$app->v3toysSettings->shopPersonType)
        {
            throw new Exception('Магазин не настроен, не указан профиль покупателя');
        }

        if (!$shopBuyer = $shopPersonType->getShopBuyers()->andWhere(['cms_user_id' => $this->user->id])->one())
        {
            $shopBuyer                  = $shopPersonType->createModelShopBuyer();
            $shopBuyer->cms_user_id     = $this->user->id;
            $shopBuyer->name            = $this->name . " [{$this->email}]";

            if (!$shopBuyer->save())
            {
                throw new Exception('Не удалось создать профиль покупателя');
            }
        }

        return $shopBuyer;
    }


    /**
     * Телефон в правильном формате для апи
     *
     * @return string
     */
    public function getPhoneFotApi()
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


    public function getShippindData()
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
     * @return array
     */
    public function getApiRequestData()
    {
        return [
            'order_id'              => $this->id,
            'fake'                  => 0,
            'full_name'             => $this->name,
            'comment'               => $this->comment,
            'phone'                 => $this->getPhoneFotApi(),
            'email'                 => $this->email,
            'created_at'            => date("Y-m-d H:i:s", $this->created_at),
            'products'              => $this->products,
            'shipping_method'       => $this->shipping_method,
            'shipping_cost'         => 0,
            'shipping_data'         => $this->getShippindData(),
        ];
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
    public function getShopOrder()
    {
        return $this->hasOne(ShopOrder::className(), ['id' => 'shop_order_id']);
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

    /**
     * @var string имя клиента
     */
    //public $name;

    /**
     * @var string телефон клиента формат ^7[3,4,8,9][0-9]{9}$
     */
    //public $phone;

    /**
     * @var string email клиента
     */
    //public $email;

    /**
     * комментарий от клиента, пожелания и данные по оплате так же указываются здесь
     * @var string
     */
    //public $comment;

    /**
     * способ доставки
            доступны только следующие значения:
            COURIER - доставка курьером
            PICKUP - самовывоз
            POST - доставка Почтой России
     *
     * @var string
     */
    //public $shipping_method;

        /**
        *   shipping_data
                если
                shipping_method
                =
                COURIER
         */
    /**
     *      город, в который нужно осуществить доставку
            доступны только следующие значения:
            Москва до МКАД
            Московская область
            Санкт-Петербург до КАД
            Ленинградская область
            Брянск
            Владимир
            Вологда
            Екатеринбург
            Иваново
            Казань
            Калуга
            Кострома
            Курск
            Нижний Новгород
            Орел
            Ростов-на-Дону
            Рязань
            Тверь
            Тула
            Тюмень
            Челябинск
            Ярославль
     * @var string
     */
    //public $courier_city;

    /**
     * адрес доставки в городе
     *
     * @var  string
     */
    //public $courier_address;



        /**
         *  shipping_data
            если
            shipping_method
            =
            PICKUP
         */



    /**
     * город, в котором будет самовывоз
     *
     * доступны только следующие значения:
            Москва
            Санкт-Петербург
            Великий Новгород
            Волгоград
            Брянск
            Воронеж
            Выборг
            Вологда
            Иваново
            Екатеринбург
            Казань
            Калуга
            Киров
            Краснодар
            Нижний Новгород
            Новороссийск
            Омск
            Орел
            Пермь
            Псков
            Ростов-на-Дону
            Рязань
            Смоленск
            Самара
            Тверь
            Тула
            Тюмень
            Челябинск
            Ярославль
            Новосибирск
            Астрахань
            Белгород
            Курск
            Ступино
            Солнечногорск
            Сергиев Посад
     *
     * @var string
     */
    //public $pickup_city;

    /**
     *  номер пункта самовывоза в выбранном городе (номера смотреть здесь)
        если в выбранном городе только один пункт самовывоза - указать значение 1
     *
     * @var int
     */
    //public $pickup_point_id = 1;

        /**
         *  shipping_data
            если
            shipping_method
            =
            POST
         */

    /**
     * почтовый индекс
     * @var string
     */
    //public $post_index;
    /**
     * 	регион
     * @var string
     */
    //public $post_region;

    /**
     * область
     * @var string
     */
    //public $post_area;

    /**
     * город
     * @var string
     */
    //public $post_city;

    /**
     * адрес в городе
     * @var string
     */
    //public $post_address;

    /**
     * полное ФИО получателя
     * @var string
     */
    //public $post_recipient;

    /**
     *  признак оформления заказ со статусом "Прин. ночь"
        возможные значения:
     * @var boolean
     */
    //public $is_call_me_15_min = true;
}
