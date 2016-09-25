<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
namespace v3toys\skeeks\widgets\delivery;

use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryMapWidgetAsset;
use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryRadioWidgetAsset;
use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Class V3toysDeliveryFastWidget
 * @package v3toys\skeeks\widgets\delivery
 */
class V3toysDeliveryInputWidget extends InputWidget
{
    public static $autoIdPrefix = 'V3toysDeliveryInputWidget';

    public $wrapperOptions         = [];
    public $clientOptions   = [];

    public $viewFile = 'default-radio';

    public function init()
    {
        parent::init();

        $this->options['id']        = $this->id;
        $this->wrapperOptions['id']  = $this->id . "-wrapper";

        $this->clientOptions['id']  = $this->id;
        $this->clientOptions['wrapperId']  = $this->wrapperOptions['id'];

    }

    public function run()
    {
        if ($this->hasModel())
        {
            $formElement = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else
        {
            $formElement = Html::textInput($this->name, $this->value, $this->options);
        }

        V3toysDeliveryRadioWidgetAsset::register($this->view);

        $js = Json::encode($this->clientOptions);

        $this->view->registerJs(<<<JS
new sx.classes.V3toysDeliveryRadio({$js});
JS
);

        return $this->render($this->viewFile, [
            'formElement' => $formElement
        ]);
    }

    /**
     * @return string
     */
    public function getCurrentValue()
    {
        if ($this->hasModel())
        {
            return (string) $this->model->{$this->attribute};
        } else
        {
            return $this->value;
        }
    }
}