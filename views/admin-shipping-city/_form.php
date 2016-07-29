<?php

use yii\helpers\Html;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;
use skeeks\cms\models\Tree;

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\CmsLang */
?>
<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shipping_type')->listBox(\v3toys\skeeks\models\V3toysOrder::getShippingMethods(), ['size' => 1]); ?>

    <?= $form->field($model, 'name')->textInput(); ?>
    <?= $form->field($model, 'price'); ?>

    <?= $form->field($model, 'description')->textarea(); ?>

    <?= $form->buttonsStandart($model) ?>

<?php ActiveForm::end(); ?>