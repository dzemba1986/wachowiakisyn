<?php

namespace frontend\modules\seu\controllers\network;

use common\models\seu\network\Subnet;
use common\models\seu\network\SubnetSearch;
use yii\base\Exception;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SubnetController extends Controller {
    
    public function behaviors() {
        
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['create', 'update']
            ],
        ];
    }
    
    public function actionIndex($vlan) {
        
		$subnet = new SubnetSearch();
		$dataProvider = $subnet->search(\Yii::$app->request->queryParams);
		
		$dataProvider->query->andWhere(['vlan_id' => $vlan]);
	
		return $this->renderAjax('index', [
			'subnet' => $subnet,
			'dataProvider' => $dataProvider,
			'vlan' => $vlan	
		]);
	}
	
	public function actionCreate($vlan) {
	    
		$subnet = new Subnet();
		$subnet->scenario = Subnet::SCENARIO_CREATE;
	
		$request = \Yii::$app->request;
	
		if ($subnet->load($request->post())) {
			$subnet->vlan_id = $vlan;
			
			try {
				if(!$subnet->save()) throw new Exception('Problem z zapisem podsieci');
				
				return 1;
			} catch (\Throwable $t) {
				var_dump($subnet->errors);
				var_dump($t->getMessage());
				exit();
			}
		} else {
			return $this->renderAjax('create', [
				'subnet' => $subnet,
			]);
		}
	}
	
	public function actionUpdate($id) {
	    
		$subnet = $this->findModel($id);
		$subnet->scenario = Subnet::SCENARIO_UPDATE;
	
		$request = \Yii::$app->request;
	
		if($subnet->load($request->post())){
			try {
				if(!$subnet->save()) throw new Exception('Problem z zapisem podsieci');
					
				return 1;
			} catch (Exception $e) {
				var_dump($subnet->errors);
				exit();
			}
		} else {
			return $this->renderAjax('update', [
				'subnet' => $subnet
			]);
		}
    }
	
	public function actionDelete($id)
	{
		if($id){
		    $subnet = $this->findModel($id);
		    
			if ($subnet->getIps()->count() > 0)
				return 'Podsieć wykorzystywana';
			else
				$subnet->delete();
			
			return 1;
		} else
			return 0;
	}
	
	public function actionList($vlanId) {
	    
	    $subnets = Subnet::find()->where(['vlan_id' => $vlanId])->orderBy('desc')->all();
	    
	    if(!empty($subnets)){
	        foreach ($subnets as $subnet){
	            echo '<option value="' . $subnet->id . '">' . $subnet->ip . ' - ' . $subnet->desc . '</option>';
	        }
	    } else {
	        echo '<option>-</option>';
	    }
	}
	
    protected function findModel($id)
    {
        if (($model = Subnet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
