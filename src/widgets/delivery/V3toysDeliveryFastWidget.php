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
 * Быстрый виджет по доставке, например возле страницы 1 продукта
 *
 * Class V3toysDeliveryFastWidget
 * @package v3toys\skeeks\widgets\delivery
 */
class V3toysDeliveryFastWidget extends Widget
{
    public $options = [];

    public $clientOptions = [];

    public $viewFile = 'default-fast';


    public function init()
    {
        parent::init();

        $this->options['id'] = $this->id;
        $this->clientOptions['id'] = $this->id;
    }

    public function run()
    {
        //Если данные по доставке закешированы просто рисуем их иначе делаем ajax запрос, чтобы заполнился кеш
        if (\Yii::$app->v3toysSettings->isCurrentShippingCache) {
            return $this->render($this->viewFile);
        } else {
            $js = \yii\helpers\Json::encode([
                'backend' => \yii\helpers\Url::to('/v3toys/cart/get-current-shipping')
            ]);

            $this->view->registerJs(<<<JS
    (function(sx, $, _)
    {
        sx.classes.GetShipping = sx.classes.Component.extend({

            _onDomReady: function()
            {
                var self = this;

                _.delay(function()
                {
                    sx.ajax.preparePostQuery(self.get('backend')).execute();
                }, 500);
            }
        });

        new sx.classes.GetShipping({$js});

    })(sx, sx.$, sx._);
JS
            );
        }
    }
}