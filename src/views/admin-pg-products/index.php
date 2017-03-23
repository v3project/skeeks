<?
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 16.07.2016
 */
/* @var $this yii\web\View */


$query = (new \yii\db\Query())
            ->from('apiv5.product')
;
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => $query,
    'db'    => \Yii::$app->dbV3project
])
?>

<? $pjax = \skeeks\cms\modules\admin\widgets\Pjax::begin(); ?>

<?
$js = \yii\helpers\Json::encode([
    'backend' => \yii\helpers\Url::to(['/v3toys/admin-pg-products/add'])
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

            $('.sx-add').on('click', function()
            {
                var action = $(this).data('action');
                console.log(action);
                console.log(backend);
                
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
        'searchModel'   => $searchModel,
        'dataProvider'  => $dataProvider
    ]); ?>

    <?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
        'dataProvider'      => $dataProvider,
        //'filterModel'       => $searchModel,
        'pjax'              => $pjax,
        'autoColumns'              => false,
        'enabledCheckbox'              => false,
        //**/'adminController'   => $controller,
        'settingsData' => [
            'orderBy' => []
        ],
        'columns' =>
        [
            [
                'format' => 'raw',
                'value' => function($data)
                {
                    $id = \yii\helpers\ArrayHelper::getValue($data, 'id');
                    if (!$id)
                    {
                        return null;
                    }

                    return \yii\helpers\Html::a($id, "http://www.v3toys.ru/index.php?nid=". $id, [
                        'target' => '_blank'
                    ]);
                }
            ],

            [
                'format' => 'raw',
                'value' => function($data)
                {
                    $id = \yii\helpers\ArrayHelper::getValue($data, 'id');
                    if (!$id)
                    {
                        return null;
                    }

                    $query = (new \yii\db\Query())
                        ->from('apiv5.affproduct')
                        ->andWhere(['product_id' => $id])
                    ;

                    $affProduct = $query->one(\Yii::$app->dbV3project);
                    if ($affProduct)
                    {
                        //Уже в базе v3
                        $result = "Добавлен: " . \Yii::$app->formatter->asDatetime(strtotime(
                            \yii\helpers\ArrayHelper::getValue($affProduct, 'created_at')
                        ));
                        $result .= "<br />Обновлен: " . \Yii::$app->formatter->asDatetime(strtotime(
                            \yii\helpers\ArrayHelper::getValue($affProduct, 'updated_at')
                        ));

                        if ($title = \yii\helpers\ArrayHelper::getValue($affProduct, 'title'))
                        {
                            $result .= "<span style='color: green;'><br /> + Характеристики</span>";
                        } else
                        {
                            $result .= "<span style='color: red;'><br /> - Характеристики</span>";
                        }

                        if ($description = \yii\helpers\ArrayHelper::getValue($affProduct, 'description'))
                        {
                            $result .= "<span style='color: green;'><br /> + Описание</span>";
                        } else
                        {
                            $result .= "<span style='color: red;'><br /> - Описание</span>";
                        }

                    } else
                    {
                        $result = \yii\helpers\Html::a('Только характеристики', '#', [
                            'class' => 'btn btn-default btn-xs sx-add',
                            'data' => [
                                'action' => 'prop'
                            ]
                        ]);

                        $result .= \yii\helpers\Html::a('Только текст', '#', [
                            'class' => 'btn btn-default btn-xs sx-add',
                            'data' => [
                                'action' => 'text'
                            ]
                        ]);

                        $result .= \yii\helpers\Html::a('Добавить все', '#', [
                            'class' => 'btn btn-primary btn-xs sx-add',
                            'data' => [
                                'action' => 'all'
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

