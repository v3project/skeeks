<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 19.07.2016
 */
namespace v3toys\skeeks\forms\CreateOrderForm;

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
 * @package v3toys\skeeks\forms\CreateOrderForm
 */
class CreateOrderForm extends Model
{
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
     * @var
     */
    public $city;
    /**
     * адрес доставки в городе
     * @var
     */
    public $address;
    /**
     *  номер пункта самовывоза в выбранном городе (номера смотреть здесь)
        если в выбранном городе только один пункт самовывоза - указать значение 1
     *
     * @var
     */
    public $point_id;

    /**
     * почтовый индекс
     * @var
     */
    public $index;
    /**
     * 	регион
     * @var
     */
    public $region;

    /**
     * область
     * @var
     */
    public $area;
    public $recipient;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'required'],
            [['email'], 'email'],
            [['shipping_method'], 'string'],
            [['comment'], 'string'],
            [['address'], 'string'],
            [['index'], 'string'],
            [['region'], 'string'],
            [['recipient'], 'string'],
            [['point_id'], 'integer'],
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
            'city'                      => 'Город',
            'address'                   => 'Город',
        ];
    }
}
