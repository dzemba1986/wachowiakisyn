<?php

use common\models\crm\Task;
use common\models\crm\TaskCategory;
use kartik\grid\ActionColumn;
use kartik\grid\ExpandRowColumn;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\CheckboxColumn;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\task\models\InstallTaskSearch $searchModel
 */

echo $this->renderFile('@app/views/modal/modal_sm.php');
echo $this->renderFile('@app/views/modal/modal.php');
echo $this->renderFile('@app/views/modal/modal_lg.php');
$this->params['breadcrumbs'][] = 'Zadania';

echo GridView::widget([
    'id' => 'task-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'task-grid-pjax'
        ]
    ],
    'summary' => 'Widoczne {count} z {totalCount}',
    'resizableColumns' => false,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
    'export' => [
        'label' => 'PDF',
        'showConfirmAlert' => false,
        
    ],
    'exportConfig' => [
        'pdf' => ['label' => 'Wygeneruj PDF']
    ],
    'panel' => [
        'before' => '',
    ],
    'rowOptions' => ['class' => 'text-center'],
    'columns' => [
        [
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['task/create']), ['id' => 'add-task']),
            'class' => 'yii\grid\SerialColumn',
        ],
        [
            'class' => ExpandRowColumn::class,
            'hiddenFromExport' => false,
            'value' => function() {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function($model) {
                return $model->desc;
            },
        ],
        [
            'attribute' => 'create_at',
            'format' => ['date', 'php:Y-m-d'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'attribute' => 'create_at',
                'pickerButton' => false,
                'language' => 'pl',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ],
            'headerOptions' => ['class' => 'text-center'],
        ],
        [
            'attribute' => 'address_id',
            'value' => function ($model) {
                return Html::a($model->address_string, ['/soa/address/tabs', 'id' => $model->address_id]);
            },
            'format' => 'raw',
            'group' => true,  // enable grouping,
            'groupedRow' => true,
//             'groupOddCssClass' => 'kv-grouped-row',
//             'groupEvenCssClass' => 'kv-grouped-row',
//             'filterType' => GridView::FILTER_SELECT2,
//             'filter' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
//             'filterWidgetOptions' => [
//                 'pluginOptions' => ['allowClear' => true],
//             ],
//             'filterInputOptions' => ['placeholder' => 'Ulica'],
//             'format' => 'raw',
        ],
        [
            'attribute' => 'type_id',
            'value' => 'type.name',
            'filter' => ArrayHelper::map(TaskCategory::find()->where(['parent_id' => 0])->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Typ'],
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['style' => 'width: 17%;'],
        ],
        [
            'attribute' => 'category_id',
            'value' => 'category.name',
//             'filter' => ArrayHelper::map(TaskCategory::find()->where(['parent_id' => 0])->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Kategoria'],
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['style' => 'width: 17%;'],
        ],
        [
            'attribute' => 'subcategory_id',
            'value' => 'subcategory.name',
//             'filter'=> ArrayHelper::map(TaskSubcategory::find()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Podkategoria'],
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['style' => 'width: 17%;'],
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status == 0) return '<span class="label label-success">'. Task::STATUS[$model->status].'</span>';
                if ($model->status == 1) return '<span class="label label-danger">'. Task::STATUS[$model->status].'</span>';
                if ($model->status == 2) return '<span class="label label-info">'. Task::STATUS[$model->status].'</span>';
            },
            'filter' => Task::STATUS,
            'filterType' => GridView::FILTER_SELECT2,
            'filterOptions' => ['prompt' => ''],
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
                'options' => ['multiple' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Status'],
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['style' => 'width: 17%;'],
        ],
//         [
//             'attribute' => 'close_at',
//             'format' => ['date', 'php:Y-m-d'],
//             'filterType' => GridView::FILTER_DATE,
//             'filterWidgetOptions' => [
//                 'model' => $searchModel,
//                 'attribute' => 'close_at',
//                 'pickerButton' => false,
//                 'language' => 'pl',
//                 'pluginOptions' => [
//                     'format' => 'yyyy-mm-dd',
//                     'todayHighlight' => true,
//                 ]
//             ],
//         ],
        [
            'attribute' => 'programme',
            'header' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-calendar']),
            'format' => 'raw',
            'filter' => ['Nie', 'Tak'],
            'value' => function ($model) {
                $span = Html::tag('span', '', ['class' => 'glyphicon glyphicon-calendar']);
                if ($model->programme) {
                    $range = date('Y-m-d H:i', strtotime($model->start_at)) . ' - ' . date('H:i', strtotime($model->end_at));                    
                    return Html::a($span, ['calendar-ajax', 'date' => date('Y-m-d', strtotime($model->start_at))], [
                        'data-toggle' => 'tooltip', 'title' => $range,
                        'onclick' => "$('#modal-lg').modal('show').find('#modal-lg-content').load($(this).attr('href')); return false;"
                    ]);
                }
                else return Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-plus']), ['']);
            },
            'headerOptions' => ['class' => 'text-center'],
        ],
        [
            'class' => CheckboxColumn::class,
        ],
        [
            'class' => ActionColumn::class,
            'header' => PageSize::widget([
                'defaultPageSize' => 100,
                'sizes' => [
                    10 => 10,
                    100 => 100,
                    500 => 500,
                    1000 => 1000,
                    5000 => 5000,
                ],
                'template' => '{list}',
                'options' => ['class' => 'form-control'],
            ]),
            'mergeHeader' => true,
            'template' => '{view} {update} {config} {close}',
            'dropdown' => true,
            'dropdownMenu' => ['style' => 'left: -100px;'],
            'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span> Podgląd', [$model::CONTROLLER . '/view', 'id' => $key], [
                        'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
                    ]);
                    return Html::tag('li', $link, []);
                },
                'update' => function ($url, $model, $key) {
                    $link = Html::a('<span class="glyphicon glyphicon-pencil"></span> Edycja', [$model::CONTROLLER . '/update', 'id' => $key], [
                            'onclick' => "$('#modal').modal('show').find('#modal-content').load($(this).attr('href')); return false;"
                        ]);
                    return Html::tag('li', $link, []);
                },
                'close' => function ($url, $model, $key) {
                    $link = Html::a('<span class="glyphicon glyphicon-ok"></span> Zamknij', [$model::CONTROLLER . '/close', 'id' => $key], [
                            'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
                        ]);
                    return Html::tag('li', $link, []);
                },
            ],
            'visibleButtons' => [
                'update' => function ($model) { return $model->status <> 1; },
                'close' => function ($model) { return $model->status <> 1; },
            ]
        ],
    ],
]);

$js = <<<JS
$(function() {
});
JS;

$this->registerJs($js)
?>