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

class V3toysDeliveryWidget extends Widget
{
    public $viewFile    = 'default';

    public $label       = 'Условия доставки';

    public function run()
    {
        V3toysDeliveryWidgetAsset::register($this->view);

        $this->view->registerJs(<<<JS
new sx.classes.V3toysDelivery();
JS
);

        return $this->render($this->viewFile);
    }
}