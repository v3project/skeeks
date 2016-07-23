<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.07.2016
 */
namespace v3toys\skeeks\models;

use skeeks\cms\models\CmsUser;
use skeeks\cms\models\forms\SignupForm;
use skeeks\cms\shop\models\ShopBuyer;
use skeeks\cms\shop\models\ShopFuser;
use skeeks\cms\shop\models\ShopOrder;
use skeeks\cms\shop\models\ShopPaySystem;
use skeeks\cms\validators\PhoneValidator;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 *
 * Форма используется для создания заказа
 *
 * @see http://www.v3toys.ru/index.php?nid=api
 *      getOrderDataById    — 3.1.7 Метод getOrderDataById - получение данных заказа по номеру
 *      createOrder         — 3.1.1 Метод createOrder - создает заказ и возвращает его номер
 *
 *
 * @property $user CmsUser
 *
 * Class CreateOrderForm
 * @package v3toys\skeeks\forms
 */
class V3toysOrder extends Model
{
    const SHIPPING_METHOD_COURIER   = 'COURIER';
    const SHIPPING_METHOD_PICKUP    = 'PICKUP';
    const SHIPPING_METHOD_POST      = 'POST';

    /**
     * @var string имя клиента
     */
    public $name;

    /**
     * @var string телефон клиента формат ^7[3,4,8,9][0-9]{9}$
     */
    public $phone;

    /**
     * @var string email клиента
     */
    public $email;

    /**
     * комментарий от клиента, пожелания и данные по оплате так же указываются здесь
     * @var string
     */
    public $comment;

    /**
     * способ доставки
            доступны только следующие значения:
            COURIER - доставка курьером
            PICKUP - самовывоз
            POST - доставка Почтой России
     *
     * @var string
     */
    public $shipping_method;

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
    public $courier_city;

    /**
     * адрес доставки в городе
     *
     * @var  string
     */
    public $courier_address;



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
    public $pickup_city;

    /**
     *  номер пункта самовывоза в выбранном городе (номера смотреть здесь)
        если в выбранном городе только один пункт самовывоза - указать значение 1
     *
     * @var int
     */
    public $pickup_point_id = 1;

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
    public $post_index;
    /**
     * 	регион
     * @var string
     */
    public $post_region;

    /**
     * область
     * @var string
     */
    public $post_area;

    /**
     * город
     * @var string
     */
    public $post_city;

    /**
     * адрес в городе
     * @var string
     */
    public $post_address;

    /**
     * полное ФИО получателя
     * @var string
     */
    public $post_recipient;

    /**
     *  признак оформления заказ со статусом "Прин. ночь"
        возможные значения:
     * @var boolean
     */
    public $is_call_me_15_min = true;


    /**
     * @var null|CmsUser
     */
    public $_user = null;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'shipping_method'], 'required'],
            [['email'], 'email'],
            [['phone'], 'string'],
            [['phone'], PhoneValidator::className()],
            [['shipping_method'], 'string'],
            [['shipping_method'], 'in', 'range' => array_keys(static::getShippingMethods())],

            //Доставка курьером
            [['courier_city'], 'string'],
            [['courier_address'], 'string'],

            //Самовывоз
            [['pickup_city'], 'string'],
            [['pickup_point_id'], 'integer'],

            //Почта
            [['post_index'], 'string'],
            [['post_region'], 'string'],
            [['post_area'], 'string'],
            [['post_city'], 'string'],
            [['post_address'], 'string'],
            [['post_recipient'], 'string'],

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
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name'                      => 'Имя и фамилия',
            'phone'                     => 'Телефон',
            'email'                     => 'Email',
            'shipping_method'           => 'Доставка',
            'comment'                   => 'Комментарий',

            'courier_city'              => 'Город',
            'courier_address'           => 'Адрес',

            'pickup_city'               => 'Город',
            'pickup_point_id'           => 'Пункт самовывоза',

            'post_index'                => 'Индекс',
            'post_region'               => 'Регион',
            'post_area'                 => 'Область',
            'post_city'                 => 'Город',
            'post_address'              => 'Адрес',
            'post_recipient'            => 'Полное ФИО получателя',

            'is_call_me_15_min'         => 'Готов принять звонок в течении 15 минут',
        ];
    }


    /**
     * @return $this
     */
    public function loadDefaultValues()
    {
        if ($this->user)
        {
            $this->email = $this->user->email;
            $this->name = $this->user->displayName;
            $this->phone = $this->user->phone;
        }

        return $this;
    }

    /**
     * Процесс создания заказа
     *
     * @return $this
     */
    public function processCreateOrder()
    {
        if (!$this->user)
        {
            //create user
            $this->user = $this->createCmsUser();

            //Авторизация пользователя
            \Yii::$app->user->login($this->user, 0);
            //throw new Exception('Не удалось создать пользователя');
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


        return $this;
    }

    /**
     * @return null|\skeeks\cms\models\User
     * @throws Exception
     */
    public function createCmsUser()
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
     * @return \common\models\User|mixed|null|\skeeks\cms\models\User|\yii\web\IdentityInterface
     */
    public function getUser()
    {
        if ($this->_user === null)
        {
            $this->_user = \Yii::$app->user->identity;
        }

        return $this->_user;
    }
    /**
     * @return \common\models\User|mixed|null|\skeeks\cms\models\User|\yii\web\IdentityInterface
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }


}
