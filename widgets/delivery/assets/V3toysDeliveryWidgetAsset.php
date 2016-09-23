<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
namespace v3toys\skeeks\widgets\delivery\assets;
use skeeks\cms\base\AssetBundle;

class V3toysDeliveryWidgetAsset extends AssetBundle
{
    public $sourcePath = '@v3toys/skeeks/widgets/delivery/assets/src';

    public $css = [
        'css/delivery-style.css',
    ];
    public $js = [
        'https://api-maps.yandex.ru/2.1/?lang=ru_RU',
        'plugins/jquery.fastLiveFilter.js',
        'js/delivery.js',
    ];
    public $depends = [
        'skeeks\sx\assets\Core',
    ];
}