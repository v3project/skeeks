<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 03.07.2017
 */
/* @var $this yii\web\View */
/* @var $property \v3toys\parsing\models\V3toysProductProperty */
/* @var $controller \skeeks\cms\backend\controllers\BackendModelController */
/* @var $action \skeeks\cms\backend\actions\BackendModelCreateAction|\skeeks\cms\backend\actions\IHasActiveForm */
$controller = $this->context;
$action = $controller->action;
?>
<?php $form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin(); ?>
<?php echo $form->errorSummary($property); ?>
<?= $form->field($property, 'v3toys_id'); ?>
<?= $form->field($property, 'hero_id')->textInput(['disabled' => 'disabled']); ?>
<?= $form->field($property, 'series_id')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'sex')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'age_from')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'age_to')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'to_who')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'model')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'color')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'scale')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'number_of_parts')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'complect')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'players_number')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'allowable_weight')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'availability_batteries')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'batteries_type')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'game_time')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'charge_time')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'range')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'composition')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'number_pages')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'volume')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'size_of_box')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'size_of_toy')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'producing_country')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'packing')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'extra')->textarea(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'sku')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'stock_barcode')->textInput(['disabled' => 'disabled']);; ?>
<?= $form->field($property, 'v3toys_brand_name')->textInput(['disabled' => 'disabled']);; ?>
<? /*= $form->field($property, 'v3toys_title')->textInput(['disabled' => 'disabled']);; */ ?><!--
    --><? /*= $form->field($property, 'v3toys_description')->textInput(['disabled' => 'disabled']);; */ ?>

<?= $form->buttonsStandart($property) ?>
<?php echo $form->errorSummary($property); ?>
<?php \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); ?>
