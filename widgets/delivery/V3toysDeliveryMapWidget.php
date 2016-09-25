<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
namespace v3toys\skeeks\widgets\delivery;

use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryMapWidgetAsset;
use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Быстрый виджет по доставке, например возле страницы 1 продукта
 *
 * Class V3toysDeliveryFastWidget
 * @package v3toys\skeeks\widgets\delivery
 */
class V3toysDeliveryMapWidget extends InputWidget
{
    public static $autoIdPrefix = 'V3toysDeliveryMapWidget';

    public $wrapperOptions     = [];

    public $clientOptions     = [];

    public $viewFile    = 'default-map';

    public $mapId       = '';

    public function init()
    {
        parent::init();

        $this->wrapperOptions['id']  = $this->options['id'] . "-wrapper";

        $this->clientOptions['id']  = $this->options['id'];
        $this->clientOptions['wrapperId']  = $this->wrapperOptions['id'];


        $this->mapId = $this->id . "-map";
        $this->clientOptions['mapId']  = $this->mapId;

        Html::addCssClass($this->wrapperOptions, 'order-delivery--map');

        echo \yii\helpers\Html::beginTag("div", $this->wrapperOptions);

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

        V3toysDeliveryMapWidgetAsset::register($this->view);

        $this->clientOptions['outlets'] = (array) \Yii::$app->v3toysSettings->currentShipping->isPickup ? \Yii::$app->v3toysSettings->currentShipping->outlets : [];
        $this->clientOptions['geoobject'] = \Yii::$app->dadataSuggest->address;
        $js = Json::encode($this->clientOptions);

        $this->view->registerJs(<<<JS
new sx.classes.V3toysDeliveryMap({$js});
JS
);

        echo $this->render($this->viewFile, [
            'formElement' => $formElement
        ]);
        echo \yii\helpers\Html::endTag("div");
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