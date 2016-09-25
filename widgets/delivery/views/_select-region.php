<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
/* @var $this yii\web\View */
?>
<div class="city">
    <span class="lbl">Укажите адрес:</span>
    <span class="link"><a href="#" onclick="new sx.classes.ModalRegionPageReload(); return false;" >
            <?= \Yii::$app->dadataSuggest->address ? \Yii::$app->dadataSuggest->address->unrestrictedValue : "Выбрать город"; ?>
    </a></span>
</div>
<? if (\Yii::$app->v3toysSettings->currentShipping->isCourier) : ?>
    <div class="date"><strong>Ближайшая доставка:</strong>
        <?= \Yii::$app->formatter->asDate(\Yii::$app->v3toysSettings->currentShipping->courierShippingDate); ?>
        (<?= \Yii::$app->formatter->asRelativeTime(\Yii::$app->v3toysSettings->currentShipping->courierShippingDate); ?>)
    </div>
<? endif; ?>