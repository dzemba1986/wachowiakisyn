<?php

namespace backend\controllers;

use backend\models\Subnet;
use backend\models\SubnetSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SubnetController extends Controller
{  
	public function actionIndex($vlan)
	{
		$subnet = new SubnetSearch();
		$dataProvider = $subnet->search(\Yii::$app->request->queryParams);
		
		$dataProvider->query->andWhere(['vlan_id' => $vlan]);
	
		return $this->renderAjax('index', [
			'subnet' => $subnet,
			'dataProvider' => $dataProvider,
			'vlan' => $vlan	
		]);
	}
	
	public function actionCreate($vlan = null)
	{
		$modelSubnet = new Subnet();
		$modelSubnet->scenario = Subnet::SCENARIO_CREATE;
	
		$request = \Yii::$app->request;
	
		if ($request->isAjax){
			if ($modelSubnet->load($request->post())) {
				$modelSubnet->vlan = $vlan;
				if($modelSubnet->validate()){
					try {
						if(!$modelSubnet->save())
							throw new Exception('Problem z zapisem podsieci');
							return 1;
					} catch (Exception $e) {
						var_dump($modelSubnet->errors);
						var_dump($e->getMessage());
						exit();
					}
						
				} else {
					var_dump($modelSubnet->errors);
					exit();
				}
			} else {
				return $this->renderAjax('create', [
					'modelSubnet' => $modelSubnet,
				]);
			}
		}
	}
	
	public function actionUpdate($id)
	{
		$modelSubnet = $this->findModel($id);
		$modelSubnet->scenario = Subnet::SCENARIO_UPDATE;
	
		$request = \Yii::$app->request;
	
		if($request->isAjax){
			if($modelSubnet->load($request->post())){
				if($modelSubnet->validate()){
					try {
						if(!$modelSubnet->save())
							throw new Exception('Problem z zapisem podsieci');
							return 1;
					} catch (Exception $e) {
						var_dump($modelSubnet->errors);
						exit();
					}
				}
			} else {
				return $this->renderAjax('update', [
					'modelSubnet' => $modelSubnet
				]);
			}
		}
	}
	
	public function actionDelete($id)
	{
		if($id){
			if (count($this->findModel($id)->modelIps) > 0)
				return 'Podsieć wykorzystywana';
			else
				$this->findModel($id)->delete();
			
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
