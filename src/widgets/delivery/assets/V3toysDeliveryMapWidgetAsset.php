<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */

namespace v3toys\skeeks\widgets\delivery\assets;

use skeeks\cms\base\AssetBundle;

class V3toysDeliveryMapWidgetAsset extends AssetBundle
{
    public $sourcePath = '@v3toys/skeeks/widgets/delivery/assets/src';

    public $css = [
        'css/delivery-style.css',
    ];
    public $js = [
        'plugins/jquery.fastLiveFilter.js',
        'js/delivery-map.js',
    ];
    public $depends = [
        'skeeks\sx\assets\Core',
        'skeeks\cms\ya\map\assets\YaMapAsset',
    ];
}