<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
/* @var $this yii\web\View */
/* @var $widget \v3toys\skeeks\widgets\delivery\V3toysDeliveryInputWidget */
$widget = $this->context;
?>

<?= \yii\helpers\Html::beginTag("div", $widget->wrapperOptions); ?>
    <div style="display: none;">
        <?= $formElement; ?>
    </div>
    <div class="row sx-visible-radios">
        <? if (\Yii::$app->v3toysSettings->currentShipping->isPickup) : ?>
            <div class="radio with-icon col-md-4">
                <input type="radio" name="<?= $widget->id ?>-visible-input" id="<?= $widget->id ?>-delivery-pickup"
                       value="<?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_PICKUP; ?>"
                        <?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_PICKUP == $widget->getCurrentValue() ? "checked" : ""; ?>/>
                <label for="<?= $widget->id ?>-delivery-pickup">
                    <span class="icon"><img src="<?= \v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset::getAssetUrl('img/people-self.jpg'); ?>" alt=""></span>
                    Самовывоз <span class="small">- от <?= \Yii::$app->money->convertAndFormat(
                            \Yii::$app->v3toysSettings->currentShipping->pickupMinPrice
                        ); ?></span>
                </label>
            </div>
        <? endif; ?>
        <? if (\Yii::$app->v3toysSettings->currentShipping->isPost) : ?>
            <div class="radio with-icon col-md-4">
                <input type="radio" name="<?= $widget->id ?>-visible-input" id="<?= $widget->id ?>-delivery-post"
                    value="<?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_POST; ?>"
                    <?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_POST == $widget->getCurrentValue() ? "checked" : ""; ?>/>
                <label for="<?= $widget->id ?>-delivery-post">
                    <span class="icon"><img src="<?= \v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset::getAssetUrl('img/delivery-post.png'); ?>" alt=""></span>
                    Почта России <span class="small">- <?= \Yii::$app->money->convertAndFormat(
                            \Yii::$app->v3toysSettings->currentShipping->postMinPrice
                        ); ?></span>
                </label>
            </div>
        <? endif; ?>
        <? if (\Yii::$app->v3toysSettings->currentShipping->isCourier) : ?>
            <div class="radio with-icon col-md-4">
                <input type="radio" name="<?= $widget->id ?>-visible-input" id="<?= $widget->id ?>-delivery-courier"
                    value="<?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_COURIER; ?>"
                    <?= \v3toys\skeeks\models\V3toysOrder::SHIPPING_METHOD_COURIER == $widget->getCurrentValue() ? "checked" : ""; ?>/>
                <label for="<?= $widget->id ?>-delivery-courier">
                    <span class="icon"><img src="<?= \v3toys\skeeks\widgets\delivery\assets\V3toysDeliveryWidgetAsset::getAssetUrl('img/people-courier.png'); ?>" alt=""></span>
                    Курьер <span class="small">- <?= \Yii::$app->money->convertAndFormat(
                            \Yii::$app->v3toysSettings->currentShipping->courierMinPrice
                        ); ?></span>
                </label>
            </div>
        <? endif; ?>
    </div>
<?= \yii\helpers\Html::endTag("div"); ?>