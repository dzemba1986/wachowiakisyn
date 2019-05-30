<?php

use common\models\User;
use common\models\crm\Task;
use common\models\crm\TaskCategory;
use kartik\grid\BooleanColumn;
use kartik\grid\ExpandRowColumn;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use nterms\pagesize\PageSize;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\ActionColumn;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\crm\TaskSearch $searchModel
 */
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
    'summary' => false,
    'resizableColumns' => false,
    'export' => false,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
    'columns' => [
        [
            'class' => SerialColumn::class,
            'header' => Html::a('<span class="glyphicon glyphicon-plus"></span>', ['task/create', 'addressId' => $addressId]),
        ],
        'id',
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
        ],
        [
            'attribute' => 'create_by',
            'value' => 'createBy.last_name'
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
        ],
        [
            'attribute' => 'close_by',
            'value' => function ($model) {
                return $model->close_by <> 19 ? 'closeBy.last_name' : $model->done_by;
            }
        ],
        [
            'attribute' => 'fulfit',
            'class' => BooleanColumn::class,
            'trueIcon' => GridView::ICON_ACTIVE,
            'falseIcon' => GridView::ICON_INACTIVE,
        ],
        [
            'attribute' => 'close_at',
            'format' => ['date', 'php:Y-m-d'],
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'model' => $searchModel,
                'attribute' => 'close_at',
                'pickerButton' => false,
                'language' => 'pl',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ],
        ],
        [
            'class' => '\kartik\grid\CheckboxColumn',
            
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
            'template' => '{close}',
            'dropdown' => true,
            'dropdownMenu' => ['style' => 'left: -100px;'],
            'dropdownButton' => ['label' => 'Akcje','class'=>'btn btn-secondary', 'style' => 'padding: 0px 5px'],
            'buttons' => [
                'close' => function ($url, $model, $key) {
                    $link = Html::a('<span class="glyphicon glyphicon-ok"></span> Zamknij', [$model::CONTROLLER . '/close', 'id' => $key], [
                        'onclick' => "$('#modal-sm').modal('show').find('#modal-sm-content').load($(this).attr('href')); return false;"
                    ]);
                    return Html::tag('li', $link, []);
                },
            ],
            'visibleButtons' => [
                'close' => function ($model) { return $model->status <> 1; },
            ]
        ],
    ],
]);
?>