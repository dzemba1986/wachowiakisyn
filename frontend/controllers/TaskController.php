<?php
namespace frontend\controllers;

use common\models\crm\Comment;
use common\models\crm\DeviceTaskSearch;
use common\models\seu\devices\Camera;
use frontend\modules\crm\models\forms\TaskForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;

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
            [
                'class' => AjaxFilter::className(),
                'only' => ['create', 'get-comments']
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
    		['device_type' => Camera::TYPE],
    		['or', ['task.status' => false], ['task.status' => null]]	
    	])->orderBy('status DESC, create DESC');
    				
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
    
    function actionGetComments($taskId) {
        
        $comments = Comment::find()->where(['task_id' => $taskId])->orderBy('create')->all();
        
        return $this->renderAjax('comments', [
            'comments' => $comments
        ]);
    }
}