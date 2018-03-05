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

<?= \yii\helpers\Html::beginTag("div", $widget->options); ?>
<? if (\Yii::$app->v3toysSettings->currentShipping->isPickup) : ?>
    <p>Самовывоз — <span class="val"><?= \Yii::$app->money->convertAndFormat(
                \Yii::$app->v3toysSettings->currentShipping->pickupMinPrice
            ); ?></span></p>
<? endif; ?>


<? if (\Yii::$app->v3toysSettings->currentShipping->isCourier) : ?>
    <p>Курьер на <?= \Yii::$app->formatter->asDate(\Yii::$app->v3toysSettings->currentShipping->courierShippingDate); ?>
        — <span class="val"><?= \Yii::$app->money->convertAndFormat(
                \Yii::$app->v3toysSettings->currentShipping->courierMinPrice
            ); ?></span></p>
<? endif; ?>

<? if (\Yii::$app->v3toysSettings->currentShipping->isPost) : ?>
    <p>Почта России — <span class="val"><?= \Yii::$app->money->convertAndFormat(
                \Yii::$app->v3toysSettings->currentShipping->postMinPrice
            ); ?></span></p>
<? endif; ?>

<?= \yii\helpers\Html::endTag("div"); ?>