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
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
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


    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['v3toysIdPropertyName', 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'v3toysIdPropertyName'                => 'Название параметра у товаров — v3toys id',
        ]);
    }

    public function attributeHints()
    {
        return ArrayHelper::merge(parent::attributeHints(), [
            'v3toysIdPropertyName' => 'Как называется свойство товаров, в котором храниться id товара из системы v3toys',
        ]);
    }

    public function renderConfigForm(ActiveForm $form)
    {
        echo $form->fieldSet('Общие настройки');
            echo $form->field($this, 'v3toysIdPropertyName');
        echo $form->fieldSetEnd();

    }
}
