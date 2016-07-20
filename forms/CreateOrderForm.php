<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.07.2016
 */
namespace v3toys\skeeks\forms;

use yii\base\Model;

/**
 *
 * Форма используется для создания заказа
 *
 * @see http://www.v3toys.ru/index.php?nid=api
 *      getOrderDataById    — 3.1.7 Метод getOrderDataById - получение данных заказа по номеру
 *      createOrder         — 3.1.1 Метод createOrder - создает заказ и возвращает его номер
 *
 *
 * Class CreateOrderForm
 * @package v3toys\skeeks\forms
 */
class CreateOrderForm extends Model
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
    public $pickup_point_id;

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
            [['name', 'phone', 'email'], 'required'],
            [['email'], 'email'],
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
            'post_recipient'            => 'полное ФИО получателя',
        ];
    }
}
