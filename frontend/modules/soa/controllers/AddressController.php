<?php

namespace frontend\modules\soa\controllers;

use common\models\address\Address;
use common\models\address\AddressSearch;
use common\models\crm\ConfigTask;
use common\models\crm\ConnectTask;
use common\models\crm\DisconnectTask;
use common\models\crm\FailureTask;
use common\models\crm\InstallTask;
use common\models\crm\TaskSearch;
use common\models\soa\Installation;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\crm\Task;

class AddressController extends Controller {
    
    public function behaviors() {
        
        return [
        	'access' => [
        		'class' => AccessControl::className(),
        		'rules'	=> [
        			[
        				'allow' => true,
        				'actions' => ['index', 'tabs', 'view', 'installs', 'tasks'],
        				'roles' => ['@']	
        			]	
        		]
        	],	
            [
                'class' => AjaxFilter::className(),
                'only' => ['installs', 'tasks']
            ],
        ];
    }

    public function actionIndex() {
        
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTabs($id) {
        
        $address = $this->findModel($id);
	    
        return $this->render('tabs', [
            'address' => $address,
        ]);
    }
    
    public function actionView($id) {
        
        $address = $this->findModel($id);
        $contractList = (new Query())
            ->select(['start_at', 'type' => 'type.name', 'package' => 'package.name'])
            ->from(['c' => 'connection'])
            ->join('INNER JOIN', ['type' => 'connection_package'], 'type.id = c.type_id')
            ->join('INNER JOIN', ['package' => 'connection_package'], 'package.id = c.package_id')
            ->where(['address_id' => $id])
            ->all();
        $connections = new ArrayDataProvider([
            'allModels' => $contractList,
        ]);
//         var_dump($connections); exit();
        return $this->renderAjax('view', [
            'address' => $address,
            'connections' => $connections,
        ]);
    }
    
    public function actionInstalls($id) {
        
        $address = $this->findModel($id);
        $serviceRange = $address->serviceRange;
        if ($serviceRange) {
            $installsCount = $serviceRange->installsCount;
            
            $installs = [];
            $count = 0;
            foreach ($installsCount as $key => $value) {
                $installs[$count]['name'] = Installation::TYPENAME[$key];
                $installs[$count]['count'] = $value;
                $count++;
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $installs,
            ]);   
            
            $out = $this->renderAjax('installs', [
                'dataProvider' => $dataProvider,
            ]);
        } else $out = 'Brak bazy wiedzy';
        
        return $out;
    }
    
    public function actionTasks($id) {
        
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());
        
        $dataProvider->query->select([
            'task.id', 'task.create_at', 'task.create_by', 'task.type_id', 'task.status', 'category_id', 'task.desc', 
            'device_id', 'done_by', 'close_by', 'task.close_at', 'fulfit',
        ])->andWhere(['and', ['not in', 'task.type_id', [1,8,9]], ['address_id' => $id]]);
        
        return $this->renderAjax('tasks', [
            'addressId' => $id,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id) {
        
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
