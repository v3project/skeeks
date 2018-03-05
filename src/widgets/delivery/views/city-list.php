<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 23.09.2016
 */
/* @var $this yii\web\View */
/* @var $widget \v3toys\skeeks\widgets\delivery\V3toysCityListWidget */
$widget = $this->context;
$city_static = \v3toys\skeeks\kiwi\CityStatic::$_city_static;
$oldType = null;
$total = count($city_static);
$counter = 0;
?>

<?= \yii\helpers\Html::beginTag("div", $widget->options); ?>

<? foreach ($city_static

as $c) : ?>
<? $counter++; ?>
<? if ($oldType !== $c['type']) : ?>
    <? if ($c['type'] === 1 && $oldType === 2) : ?>
        <a href="#" class="sx-more-regions">Другой город...</a>
    <? endif; ?>
<? endif; ?>

<? if ($oldType !== $c['type']) : ?>
<? if (($c['type'] === 1) && ($oldType === null || $oldType === 2)) : ?>

<div class="region-group select-region">
    <div class="region-title">
        <? endif; ?>

        <? if (($c['type'] === 8) && ($oldType === null || $oldType === 2)) : ?>
            <a href="#" class="sx-more-regions">Другой город...</a>
            <div class="region-title">Другие города</div>
        <? endif; ?>
        <? endif; ?>


        <? if ($oldType !== $c['type']) : ?>
        <? if ($c['type'] === 2 && $oldType === 1) : ?>
    </div>
    <? endif; ?>
    <? endif; ?>
    <a href="#" data-title="<?= $c['title']; ?>" class="sx-save-region"><?= $c['title']; ?></a>

    <? if ($counter == $total) : ?>
        <a href="#" class="sx-more-regions">Другой город...</a>
    <? endif; ?>

    <? $oldType = $c['type']; ?>
    <? endforeach; ?>
    <?= \yii\helpers\Html::endTag("div"); ?>

    <!--
    <div class="region-group select-region">
        <div class="region-title">
            <a href="#">Москва (в пределах МКАД)</a>
            <a href="#">Москва (за МКАД)</a>
        </div>
        <a href="#">Зеленоград</a>
        <a href="#">Троицк</a>
        <a href="#">Щербинка</a>
        <a href="#">Балашиха</a>
        <a href="#">Видное</a>
        <a href="#">Домодедово</a>
        <a href="#">Долгопрудный</a>
        <a href="#">Жуковский</a>
        <a href="#">Красногорск</a>
        <a href="#">Королев</a>
        <a href="#">Лобня</a>
        <a href="#">Мытищи</a>
        <a href="#">Подольск</a>
        <a href="#">Пушкино</a>
        <a href="#">Реутов</a>
        <a href="#">Химки</a>
        <a href="#">Щелково</a>
    </div>-->
