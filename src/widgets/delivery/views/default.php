<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
/* @var $this yii\web\View */
/* @var $widget \v3toys\skeeks\widgets\delivery\V3toysDeliveryWidget */
$widget = $this->context;
?>

<?= \yii\helpers\Html::beginTag("div", $widget->options); ?>
    <section class="sx-select-region">
        <div class="order-delivery--region">
            <? if ($widget->contentSelectRegion) : ?>
                <?= $widget->contentSelectRegion; ?>
            <? else : ?>
                <?= $this->render('_select-region'); ?>
            <? endif; ?>
        </div>

        <div class="order-delivery--radios">
            <? if ($widget->contentRadioElement) : ?>
                <?= $widget->contentRadioElement; ?>
            <? else : ?>
                <?= \v3toys\skeeks\widgets\delivery\V3toysDeliveryInputWidget::widget([
                    'name' => 'deliveryChange',
                    'options' => [
                        'class' => 'sx-deliveryChange'
                    ],
                    //'value' => 'POST'
                ]); ?>
            <? endif; ?>
        </div>
    </section>

    <section class="delivery-form delivery-page--text-block delivery-form-PICKUP">

        <? if ($widget->contentPickup) : ?>
            <?= $widget->contentPickup; ?>
        <? else : ?>
            <?= $this->render('_delivery-pickup'); ?>
        <? endif; ?>

    </section>

    <section class="delivery-form delivery-page--text-block delivery-form-POST">
        <? if ($widget->contentPost) : ?>
            <?= $widget->contentPost; ?>
        <? else : ?>
            <?= $this->render('_delivery-post'); ?>
        <? endif; ?>
    </section>

    <section class="delivery-form delivery-page--text-block delivery-form-COURIER">
        <? if ($widget->contentCourier) : ?>
            <?= $widget->contentCourier; ?>
        <? else : ?>
            <?= $this->render('_delivery-courier'); ?>
        <? endif; ?>
    </section>

<?= \yii\helpers\Html::endTag("div"); ?>