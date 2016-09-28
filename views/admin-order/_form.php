<?php
use yii\helpers\Html;
use skeeks\cms\models\Tree;

/* @var $this yii\web\View */
/* @var $model \v3toys\skeeks\models\V3toysOrder */
?>

<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' =>
    [
        'id',
        'name',
        'phone',
        'email',
        'v3toys_order_id',
        [
             'label' => 'Дата создания заказа',
             'value' => \Yii::$app->formatter->asDatetime($model->created_at),
        ]
    ]
]); ?>
<pre>
<? print_r($model->getApiRequestData()); ?>
</pre>
<pre>
<? print_r($model->dadataAddress->toArray()); ?>
</pre>
<?/*= \yii\widgets\DetailView::widget([
    'model' => $model->dadataAddress,
    'attributes' => array_keys($model->dadataAddress->toArray())
]); */?>
