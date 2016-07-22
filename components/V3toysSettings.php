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
use skeeks\cms\shop\models\ShopOrderStatus;
use skeeks\cms\shop\models\ShopPersonType;
use skeeks\widget\chosen\Chosen;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @property ShopPersonType $shopPersonType
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
            'name'          => \Yii::t('v3toys/skeeks', 'Настройки v3toys'),
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



    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['v3toysIdPropertyName', 'string'],
            ['content_ids', 'safe'],
            ['v3toysShopPersonTypeId', 'integer'],
            ['affiliate_key', 'string'],
            ['v3toysOrderStatusSubmitted', 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'v3toysIdPropertyName'          => 'Название параметра у товаров — v3toys id',
            'content_ids'                   => 'Контент свяазанный с v3project',
            'v3toysShopPersonTypeId'        => 'Профиль покупателя v3project',
            'affiliate_key'                 => 'Код аффилиата полученный в v3project',
            'v3toysOrderStatusSubmitted'    => 'Статус заказа, когда он отправлен в Submitted',
        ]);
    }

    public function attributeHints()
    {
        $link = urlencode(Url::base(true));
        $a = Html::a('http://www.seogadget.ru/ip?urls=' . $link, 'http://www.seogadget.ru/ip?urls=' . $link, ['target' => '_blank']);

        return ArrayHelper::merge(parent::attributeHints(), [
            'v3toysIdPropertyName'      => 'Как называется свойство товаров, в котором храниться id товара из системы v3toys',
            'content_ids'               => 'Обновление наличия и цен будет происходить у элементов этого выбранного контента',
            'v3toysShopPersonTypeId'    => 'Необходимо настроить тип покупателя, и его свойства, для связи с данными v3toys [ <b>php yii v3toys/init/update-person-type</b> ]',
            'affiliate_key'             => 'Ключ связан с ip адресом сайта, необходимо сообщить свой IP. Проверить IP можно тут: ' . $a,
        ]);
    }

    public function renderConfigForm(ActiveForm $form)
    {
        echo $form->fieldSet('Общие настройки');
            echo $form->field($this, 'affiliate_key');
            echo $form->field($this, 'v3toysIdPropertyName');
            echo $form->field($this, 'content_ids')->widget(Chosen::className(),[
                'multiple' => true,
                'items' => CmsContent::getDataForSelect(),
            ]);
            echo $form->field($this, 'v3toysShopPersonTypeId')->widget(Chosen::className(),[
                'items' => ArrayHelper::map(ShopPersonType::find()->all(), 'id', 'name'),
            ]);
            echo $form->field($this, 'v3toysOrderStatusSubmitted')->widget(Chosen::className(),[
                'items' => ArrayHelper::map(ShopOrderStatus::find()->all(), 'code', 'name'),
            ]);
        echo $form->fieldSetEnd();
    }

    /**
     * @return ShopPersonType
     */
    public function getShopPersonType()
    {
        return ShopPersonType::findOne((int) $this->v3toysShopPersonTypeId);
    }
}
