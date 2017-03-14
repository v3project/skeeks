<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */
$response = \Yii::$app->v3toysApi->send('getOrdersIdsByPeriod', [
    'start' => "2014-11-01 00:00:00"
]);
?>
<p>3.1.6 Метод getOrdersIdsByPeriod - получение списка номеров заказов за период времени, оформленных по телефону</p>

<? if ($response->isError) : ?>
    <? echo $response->error_code; ?>
    <? echo $response->error_message; ?>
<? else : ?>
    <? print_r($response->data); ?>
<? endif; ?>
