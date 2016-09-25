<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
/* @var $this yii\web\View */
/* @var $widget \v3toys\skeeks\widgets\delivery\V3toysDeliveryFastWidget */
$widget = $this->context;
?>
<div style="display: none;">
    <?= $formElement; ?>
</div>
<div class="sx-map-point-selected" style="display: none; margin-bottom: 10px;">
    <strong>Выбран пункт: </strong><span></span>
</div>
<div class="order-delivery--map--list">
    <div class="form-group">
        <input type="text" id="search-address" class="form-control" placeholder="Поиск по улице, метро, названию"/>
    </div>
    <ul class="scroll-list" id="search-address-list">
        <? if (\Yii::$app->v3toysSettings->currentShipping->isPickup && \Yii::$app->v3toysSettings->currentShipping->outlets) : ?>
            <? foreach(\Yii::$app->v3toysSettings->currentShipping->outlets as $outlet) : ?>
                <?= \yii\helpers\Html::beginTag('li', [
                    'data' => $outlet->toArray(),
                    'id' => "sx-outlet-" . $outlet->v3p_outlet_id
                ]); ?>
                    <a href="#" class="address-item">
                        <? if ($outlet->metro_title) : ?>
                            <span class="metro">M</span> <strong><?= $outlet->metro_title; ?></strong>
                        <? endif; ?>
                        <!--<span class="metro">M</span>--><strong>г. <?= $outlet->city; ?></strong> - <strong><?= \yii\helpers\ArrayHelper::getValue($outlet->deliveryData, 'guiding_realize_price'); ?> руб.</strong><br/>
                        <?= $outlet->address; ?>
                    </a>
                <?= \yii\helpers\Html::endTag('li'); ?>
            <? endforeach; ?>
        <? endif; ?>
    </ul>
</div>
<div class="order-delivery--map--yandex">
    <div class="order-delivery--map--yandex--in">
        <div class="yandex-map" id="<?= $widget->mapId; ?>"></div>
    </div>
</div>
