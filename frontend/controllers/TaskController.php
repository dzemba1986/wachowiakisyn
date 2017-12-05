<?php
namespace frontend\controllers;

use frontend\models\TaskForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use backend\modules\task\models\DeviceTaskSearch;

class TaskController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
            	'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create-task' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
    	$searchModel = new DeviceTaskSearch();
    	
    	$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
    	
    	$dataProvider->query->andWhere([
    		'and',
    		['is not', 'device_id', null],
    		['device_type' => 6],
    		['or', ['task.status' => false], ['task.status' => null]]	
    	])->orderBy('create DESC');
    				
    	return $this->render('index', [
    		'dataProvider' => $dataProvider,
    		'searchModel' => $searchModel,
    	]);
    }
    
    public function actionCreate()
    {
    	$model = new TaskForm();
    	if ($model->load(Yii::$app->request->post())) {
    		if ($task = $model->createTask()) {
    			return 1;
    		} else 
    			return 0;
    	}
    	
    	return $this->renderAjax('create', [
    		'model' => $model,
    	]);
    }
}