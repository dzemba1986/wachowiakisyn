<?php
namespace frontend\controllers;

use backend\models\Connection;
use backend\models\ConnectionSearch;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BoaController extends Controller {
    
    public function actionIndex($mode = 'noboa') {
        
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if ($mode == 'noboa') {
            $dataProvider->query->andWhere(['synch_date' => null, 'nocontract' => false, 'vip' => false])->andWhere(['is not', 'pay_date', null]);
            
            return $this->render('grid_noboa', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
            
        } elseif ($mode == 'boa') {
            $dataProvider->query->andWhere(['close_date' => null, 'nocontract' => false, 'vip' => false])->andWhere(['is not', 'synch_date', null]);
            
            return $this->render('grid_boa', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } elseif ($mode == 'all') {
            return $this->render('grid_all', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } 
    }
    
    public function actionView($id) {
        
        $model = $this->findModel($id);
        
        return $this->renderAjax('view', [
            'model' => $model,
            'installations' => $model->installations,
        ]);
    }
    
    public function actionSync($id)
    {
        $connection = $this->findModel($id);
        $connection->scenario = Connection::SCENARIO_UPDATE;
        
        $request = Yii::$app->request;
        
        
        $connection->synch_date = date('Y-m-d H:i:s');
        
        try {
            if(!($connection->save()))
                throw new Exception('Problem z zapisem połączenia');
                
                return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    protected function findModel($id) {
        
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
