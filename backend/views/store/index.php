<?php
use backend\models\DeviceType;
use backend\models\Manufacturer;
use backend\models\Model;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var backend\models\DeviceSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */ 

echo $this->renderFile('@backend/views/modal/modal_sm.php');
echo $this->renderFile('@backend/views/modal/modal.php');
require_once '_add_device_form.php';

$this->title = 'Magazyn';
$this->params['breadcrumbs'][] = 'SEU';
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'id' => 'store-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'store-grid-pjax'
        ]    
    ],
    'resizableColumns' => false,
    'export' => false,
    'panel' => [
        'heading'=>'Magazyn',
    ],
    'columns' => [
        [
            'header' => 'Lp.',
            'class' => 'kartik\grid\SerialColumn',
            'options' => ['style'=>'width: 4%;'],
            'mergeHeader' => true
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(DeviceType::findOrderName()->all(), 'id', 'name'),
        ],
        [
            'attribute' => 'manufacturer_id',
            'value' => 'manufacturer.name',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Manufacturer::find()->orderBy('name')->all(), 'id', 'name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Producent'],
            'format' => 'raw',
            'options' => ['style'=>'width:17%;']
        ],
        [
            'attribute' => 'model_id',
            'value' => 'model.name',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Model::find()->orderBy('name')->all(), 'id', 'name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Model'],
            'format' => 'raw',
            'options' => ['style'=>'width:17%;']
        ],
        'serial',
        'mac',
        [   
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'pageSizeParam' => 'per-page',
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                ],
                'label' => 'Ilość',
                'template' => '{label}{list}',
                'options' => ['class'=>'form-control'],
            ]),
            'class' => 'kartik\grid\ActionColumn',
            'mergeHeader' => true,
            'template' => '{update} {tree} {delete}',
            'options' => ['style' => 'width:6%;'],
            'buttons' => [
                'update' => function($url, $model, $key) {
                    $url = Url::to([$model->type->controller . '/update-store', 'id' => $key]);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => \Yii::t('yii', 'Dodaj na drzewo'),
                        'onclick' => "
                            $('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href'));
                        
                            return false;
                        "
                    ]);
                },
                'tree' => function($url, $model, $key) {
                    $url = Url::to([$model->type->controller . '/add-on-tree', 'id' => $key]);
                    return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
                        'title' => \Yii::t('yii', 'Dodaj na drzewo'),
                        'onclick' => "
                            $('#modal').modal('show').find('#modal-content').load($(this).attr('href'));
                    
                            return false;
                        "
                    ]);
                },
                'delete' => function($url, $model, $key) {
                    $url = Url::to([$model->type->controller . '/delete-from-store', 'id' => $key]);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => \Yii::t('yii', 'Usuń'),
                        'onclick' => "
                            $('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href'));
                        
                            return false;
                        "
                    ]);
                },
            ]
        ],  
    ]                
]);
?>