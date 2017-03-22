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
use yii\helpers\Url;

/**
 * Class V3toysCityListWidget
 *
 * @package v3toys\skeeks\widgets\delivery
 */
class V3toysCityListWidget extends Widget
{
    public $options     = [];

    public $clientOptions     = [];

    public $viewFile    = 'city-list';

    public function init()
    {
        parent::init();
        $this->options['id'] = $this->id;
        $this->clientOptions['id'] = $this->id;
    }

    public function run()
    {
        $this->clientOptions['backend'] = Url::to(['/v3toys/city/save']);
        $js = Json::encode($this->clientOptions);

        $this->view->registerJs(<<<JS
        (function(sx, $, _)
        {
            sx.classes.SaveRegion = sx.classes.Component.extend({

                _init: function()
                {},

                _onDomReady: function()
                {
                    var self = this;

                    jQuery(".sx-save-region").on('click', function()
                    {
                        var title = jQuery(this).data('title');
                        self.save(title);
                        return false;
                    });
                },

                save: function(title)
                {
                    var self = this;

                    var blocker = new sx.classes.Blocker("#" + this.get('id'));
                    blocker.block();

                    var ajax = sx.ajax.preparePostQuery(this.get('backend'), {
                        'title' : title
                    });

                    var handler = new sx.classes.AjaxHandlerStandartRespose(ajax, {
                        //'blocker': blocker,
                        'enableBlocker': false
                    });


                    handler.bind('success', function()
                    {
                        _.delay(function()
                        {
                            window.location.reload();
                        }, 400);
                    });
                    handler.bind('error', function()
                    {
                        blocker.unblock();

                    });

                    ajax.execute();
                    return this;
                },

                getJWrapper: function()
                {
                    return jQuery('#' + this.get('id'));
                }
            });
        })(sx, sx.$, sx._);

        new sx.classes.SaveRegion({$js});
JS
);

        return $this->render($this->viewFile);
    }
}