<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
namespace v3toys\skeeks\widgets\delivery;

use v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset;
use yii\base\Widget;
use yii\helpers\Json;

/**
 * Мощьный полноценный виджет об условиях доставки
 *
 * Class V3toysDeliveryWidget
 * @package v3toys\skeeks\widgets\delivery
 */
class V3toysDeliveryWidget extends Widget
{
    public $options     = [];

    public $clientOptions     = [];

    public $viewFile    = 'default';

    public $label       = 'Условия доставки';

    /**
     * @var string контент для почты
     */
    public $contentPost       = '';
    /**
     * @var string контент самовывоза
     */
    public $contentPickup     = '';
    /**
     * @var string контент для курьера
     */
    public $contentCourier    = '';

    /**
     * @var string Выбор региона
     */
    public $contentSelectRegion    = '';
    public $contentRadioElement    = '';


    public function init()
    {
        parent::init();

        $this->options['id'] = $this->id;
        $this->clientOptions['id'] = $this->id;
    }

    public function run()
    {
        V3toysDeliveryWidgetAsset::register($this->view);

        $js = Json::encode($this->clientOptions);

        $this->view->registerJs(<<<JS
new sx.classes.V3toysDelivery({$js});
JS
);

        return $this->render($this->viewFile);
    }
}