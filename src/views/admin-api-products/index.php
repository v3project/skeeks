<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
$response = \Yii::$app->v3toysApi->getProductsDataByIds(['in_stock' => 1]);
?>
<p>3.1.3 Метод getProductsDataByIds - получение данных о товаре по коду</p>

<? if ($response->isError) : ?>
    <p>Нет доступа к БД V3Project в рамках V3ProjectAPIv5</p>
    <? echo $response->error_code; ?>
    <? echo $response->error_message; ?>
<? else : ?>
    <?/*
        print_r($response->data);
    */?>
    <?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'models' => (array) $response->data
        ]),
        'columns' =>
        [
            'id',
            'title',
            'deleted',
            'quantity',
            'buy_price',
            'base_price',
            'mr_price',
            'price',
            'excl_quantity',
            'sku',
            'barcode',
            'brand',
        ]
    ]); ?>
<? endif; ?>
