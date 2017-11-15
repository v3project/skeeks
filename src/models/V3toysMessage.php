<?php

namespace v3toys\skeeks\models;

use skeeks\cms\models\behaviors\HasJsonFieldsBehavior;
use skeeks\cms\models\Core;
use skeeks\cms\shop\models\ShopCmsContentElement;
use skeeks\cms\validators\PhoneValidator;
use skeeks\modules\cms\money\Money;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%v3toys_message}}".
 *
 * @property integer $id
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property string $full_name
 * @property string $phone
 * @property string $email
 * @property string $comment
 * @property string $products
 * @property string $status_name
 *
 * @property string $phoneForApi
 * @property array $productsForApi
 *
 * @property V3toysOrderBasket[] $baskets
 *
 * @property Money $moneyOriginal
 * @property Money $money
 *
 * @property CmsUser $user
 */
class V3toysMessage extends Core
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%v3toys_message}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            HasJsonFieldsBehavior::className() =>
                [
                    'class' => HasJsonFieldsBehavior::className(),
                    'fields' => ['products']
                ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                [['created_by', 'updated_by', 'created_at', 'updated_at', 'user_id'], 'integer'],
                //[['full_name'], 'required'],
                [['comment', 'status_name'], 'string'],
                [['full_name', 'email'], 'string', 'max' => 255],
                [['phone'], 'string', 'max' => 50],
                [
                    ['user_id'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => \Yii::$app->user->identityClass,
                    'targetAttribute' => ['user_id' => 'id']
                ],
                [
                    ['created_by'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => \Yii::$app->user->identityClass,
                    'targetAttribute' => ['created_by' => 'id']
                ],
                [
                    ['updated_by'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => \Yii::$app->user->identityClass,
                    'targetAttribute' => ['updated_by' => 'id']
                ],

                [['email'], 'email'],
                [['full_name'], 'default', 'value' => 'Покупатель'],

                [['products'], 'safe'],
                [['products'], 'required'],

                [['phone'], 'required'],

                [['phone'], PhoneValidator::className()],

            ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => Yii::t('app', 'ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'user_id' => Yii::t('app', 'User ID'),
            'full_name' => Yii::t('app', 'Имя клиента'),
            'phone' => Yii::t('app', 'Телефон'),
            'email' => Yii::t('app', 'Email'),
            'comment' => Yii::t('app', 'Комментарий'),
            'products' => Yii::t('app', 'Товары'),
            'status_name' => Yii::t('app', 'Статус'),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * @param $cmsElementId
     * @param int $quantity
     *
     * @return $this
     * @throws Exception
     */
    public function addProduct($cmsElementId, $quantity = 1)
    {
        /**
         * @var $shopElement ShopCmsContentElement
         */
        $shopElement = ShopCmsContentElement::findOne($cmsElementId);
        if (!$shopElement) {
            throw new Exception('Этот продукт не найден');
        }

        $products = [];

        $products[] = [
            'v3toys_product_id' => (int)$shopElement->relatedPropertiesModel->getAttribute(\Yii::$app->v3toysSettings->v3toysIdPropertyName),
            'price' => $shopElement->shopProduct->baseProductPriceValue,
            'quantity' => $quantity,
            'name' => (string)$shopElement->name,
            'product_id' => (int)$shopElement->id,
        ];

        $this->products = ArrayHelper::merge((array)$this->products, $products);


        return $this;
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

        foreach ($this->baskets as $basket) {
            $money = $money->add($basket->moneyTotal);
        }

        return $money;
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        return $this->moneyOriginal;
    }

    protected $_baskets = null;

    /**
     * @return V3toysOrderBasket[]
     */
    public function getBaskets()
    {
        if ($this->_baskets !== null) {
            return $this->_baskets;
        }

        $result = [];

        if ($this->products) {
            foreach ($this->products as $productData) {
                $obj = new V3toysOrderBasket();
                $obj->setAttributes($productData, false);
                $result[] = $obj;
            }
        }

        $this->_baskets = $result;

        return $this->_baskets;
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

        return (string)$phone;
    }

    /**
     * @return array
     */
    public function getProductsForApi()
    {
        $result = [];

        if ($this->products) {
            foreach ((array)$this->products as $productdata) {
                $result[] = [
                    'product_id' => ArrayHelper::getValue($productdata, 'v3toys_product_id'),
                    'price' => ArrayHelper::getValue($productdata, 'price'),
                    'quantity' => ArrayHelper::getValue($productdata, 'quantity'),
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
            'message_id' => $this->id,
            'fake' => 0,
            'full_name' => $this->full_name,
            'comment' => $this->comment,
            'phone' => $this->phoneForApi,
            'email' => $this->email,
            'created_at' => date("Y-m-d H:i:s", $this->created_at),
            'products' => $this->productsForApi,
        ];
    }
}