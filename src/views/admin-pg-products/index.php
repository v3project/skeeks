<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */


$query = (new \yii\db\Query())
    ->from('apiv5.product');
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => $query,
    'db' => \Yii::$app->dbV3project
])
?>

<? $pjax = \skeeks\cms\modules\admin\widgets\Pjax::begin(); ?>

<?
$js = \yii\helpers\Json::encode([
    'backend' => \skeeks\cms\helpers\UrlHelper::construct(['/v3toys/admin-pg-products/add'])->enableAdmin()->toString()
]);

$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.V3Project = sx.classes.Component.extend({
    
        _init: function()
        {
            
        },
        
        _onDomReady: function()
        {
            var backend = this.get('backend');

            var self = this;
            
            $('.sx-add').on('click', function()
            {
                var jBtn = $(this); 
                var action = $(this).data('action');
                var id = $(this).data('id');
                var id = $(this).data('id');
                /*console.log(id);
                console.log(action);
                console.log(backend);
                console.log('--------');*/
                
                var ajax = sx.ajax.preparePostQuery(self.get('backend'), {
                    'id': id, 
                    'action': action, 
                });
                
                ajax.bind('success', function(e, result)
                {
                    //window.location.reload();
                    jBtn.hide();
                    
                });
                
                ajax.bind('error', function(e, result)
                {
                    //window.location.reload();
                    jBtn.hide();
                });
                
                ajax.execute();
                
                return false;
            });
        },
        
        _onWindowReady: function()
        {}
    });
    
    new sx.classes.V3Project({$js});
})(sx, sx.$, sx._);
JS
);
?>

<?php echo $this->render('_search', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider
]); ?>

<?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
    'dataProvider' => $dataProvider,
    //'filterModel'       => $searchModel,
    'pjax' => $pjax,
    'autoColumns' => false,
    'enabledCheckbox' => false,
    //**/'adminController'   => $controller,
    'settingsData' => [
        'orderBy' => []
    ],
    'columns' =>
        [
            [
                'format' => 'raw',
                'value' => function ($data) {
                    $id = \yii\helpers\ArrayHelper::getValue($data, 'id');
                    if (!$id) {
                        return null;
                    }

                    return \yii\helpers\Html::a($id, "http://www.v3toys.ru/index.php?nid=" . $id, [
                        'target' => '_blank'
                    ]);
                }
            ],

            [
                'format' => 'raw',
                'value' => function ($data) {
                    $id = \yii\helpers\ArrayHelper::getValue($data, 'id');
                    if (!$id) {
                        return null;
                    }

                    $query = (new \yii\db\Query())
                        ->from('apiv5.affproduct')
                        ->andWhere(['product_id' => $id]);

                    $affProduct = $query->one(\Yii::$app->dbV3project);
                    if ($affProduct) {
                        //Уже в базе v3
                        $result = "Добавлен: " . \Yii::$app->formatter->asDatetime(strtotime(
                                \yii\helpers\ArrayHelper::getValue($affProduct, 'created_at')
                            ));
                        $result .= "<br />Обновлен: " . \Yii::$app->formatter->asDatetime(strtotime(
                                \yii\helpers\ArrayHelper::getValue($affProduct, 'updated_at')
                            ));

                        if ($title = \yii\helpers\ArrayHelper::getValue($affProduct, 'title')) {
                            $result .= "<span style='color: green;'><br /> + Характеристики</span>";
                        } else {
                            $result .= "<span style='color: red;'><br /> - Характеристики</span>";
                        }

                        if ($description = \yii\helpers\ArrayHelper::getValue($affProduct, 'description')) {
                            $result .= "<span style='color: green;'><br /> + Описание</span>";
                        } else {
                            $result .= "<span style='color: red;'><br /> - Описание</span>";
                        }

                    } else {
                        $result = \yii\helpers\Html::a('Только характеристики', '#', [
                            'class' => 'btn btn-default btn-xs sx-add',
                            'data' => [
                                'action' => 'prop',
                                'id' => $id
                            ]
                        ]);

                        $result .= \yii\helpers\Html::a('Только текст', '#', [
                            'class' => 'btn btn-default btn-xs sx-add',
                            'data' => [
                                'action' => 'text',
                                'id' => $id
                            ]
                        ]);

                        $result .= \yii\helpers\Html::a('Добавить все', '#', [
                            'class' => 'btn btn-primary btn-xs sx-add',
                            'data' => [
                                'action' => 'all',
                                'id' => $id
                            ]
                        ]);


                        $result = \yii\helpers\Html::a('Добавить', '#', [
                            'class' => 'btn btn-primary sx-add',
                            'data' => [
                                'action' => 'text',
                                'id' => $id
                            ]
                        ]);
                    }


                    return $result;
                }
            ],
            'keywords',
            'guiding_available_quantity',
            'quiding_buy_price',
            'quiding_realize_price',
            'mr_price',
            'sku',
        ]
]); ?>

<? $pjax::end(); ?>

