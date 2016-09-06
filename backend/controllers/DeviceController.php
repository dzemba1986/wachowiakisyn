<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\Device;
use backend\models\DeviceSearch;
use yii\db\Query;
use backend\models\DeviceFactory;
use yii\widgets\ActiveForm;
use backend\models\Address;
use backend\models\Ip;

class DeviceController extends Controller
{
	public function actionTabsView($id)
	{
		return $this->renderPartial('tabs-view', [
			'modelDevice' => $this->findModel($id),
		]);
	}
	
	public function actionTabsUpdate($id)
	{
		return $this->renderPartial('tabs-update', [
				'modelDevice' => $this->findModel($id),
		]);
	}
	
	public function actionView($id)
    { 	
        return $this->renderPartial('view', [
            'modelDevice' => $this->findModel($id),
        	'modelIps' => $this->findModel($id)->modelIps	
        ]);
    }
    
    public function actionStore()
    {
        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->andWhere(['address' => NULL]);

        return $this->render('store', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Updates an existing Modyfication model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelDevice = $this->findModel($id);
        $modelDevice->scenario = Device::SCENARIO_UPDATE;
        $modelAddress = $modelDevice->modelAddress;      
        
        $request = Yii::$app->request;

        if ($request->isAjax){	
        	if($modelAddress->load($request->post()) && $modelDevice->load($request->post())){
        		if($modelAddress->validate()){
        			if(!empty($modelAddress->dirtyAttributes)){
        				
        				$newModelAddress = new Address();
        				$newModelAddress->ulica = $modelAddress->ulica;
        				$newModelAddress->dom = $modelAddress->dom;
        				$newModelAddress->dom_szczegol = $modelAddress->dom_szczegol;
        				$newModelAddress->lokal = $modelAddress->lokal;
        				$newModelAddress->lokal_szczegol = $modelAddress->lokal_szczegol;
        				
	        			try {
							if(!$newModelAddress->save())
								throw new Exception('Problem z zapisem adresu');
							
							$modelDevice->address = $newModelAddress->id;	
							//return 1;
						} catch (Exception $e) {
							var_dump($newModelAddress->errors);
							var_dump($e->getMessage());
							exit();
						}
        			}
        		} else {
        			var_dump($modelAddress->errors);
        			exit();
        		}
        		
        		if($modelDevice->validate()){
        			
        			try {
        				if(!$modelDevice->save())
        					throw new Exception('Problem z zapisem urządzenia');
        				return 1;
        			} catch (Exception $e) {
        				var_dump($modelDevice->errors);
        				var_dump($e->getMessage());
        				exit();
        			}
        		} else {
        			var_dump($modelDevice->errors);
        			exit();
        		}
        	} else {
	            return $this->renderAjax('update', [
	                'modelDevice' => $modelDevice,
            		'modelAddress' => $modelAddress,
	            ]);
	        }
        }
    }

    /**
     * Deletes an existing Modyfication model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
	public function actionList($q = null, $id = null, $type = null, $dist = null) {
		
	    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    
	    $out = ['results' => ['id' => '', 'concat' => '']];
	    
	    if (!is_null($q)) {

	    	$query = new Query();
	    	$query->select(['d.id', new \yii\db\Expression("
	    		CASE
	    			WHEN d.name IS NOT NULL OR d.name <> '' THEN
	    				CONCAT(d.name, ' - ', adrs.name, '  ', dom, dom_szczegol, ' - ', '[', ip, ']')
	    			WHEN pietro IS NULL THEN	
	    				CONCAT(adrs.name, ' ', dom, dom_szczegol, ' - ', '[', ip, ']')
	    			ELSE
	    				CONCAT(adrs.name, ' ', dom, dom_szczegol, ' (piętro', pietro, ')', ' - ', '[', ip, ']')
	    		END	
	    	")])
	    	->from('device d')
	    	->join('INNER JOIN', 'address a', 'a.id = d.address')
	    	->join('LEFT JOIN', 'ip', 'ip.device = d.id AND ip.main = true')
	    	->join('INNER JOIN', 'address_short adrs', 'adrs.t_ulica = a.t_ulica')
	    	->where(['like', new \yii\db\Expression("CONCAT(adrs.name, ' ', dom, dom_szczegol)"), $q])
	    	->orWhere(['like', 'd.name', $q])
	    	->limit(20);
	    	
	    	if(!is_null($type))
	    		$query->andWhere(['type' => $type]);
	    	
	    	if(!is_null($dist)) 
	    		$query->andWhere(['distribution' => $dist]);
	    	
	    	$command = $query->createCommand();
	    	$data = $command->queryAll();
	    	$out['results'] = array_values($data);
	    }
	    elseif ($id > 0) {
	    	$out['results'] = ['id' => $id, 'concat' => Device::findOne($id)->modelAddress->fullDeviceAddress];
	    }
	    return $out;
	}
	
	public function actionSelectListFromStore($q = null, $type = null) {
	
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		 
		$out = ['results' => ['id' => '', 'concat' => '']];
		 
		if (!is_null($q)) {
	
			$query = new Query();
			$query->select(['d.id', new \yii\db\Expression("
	    		CONCAT(m.name, chr(9), d.mac, ' - ', d.serial)
	    	")])
	    	->from('device d')
	    	->join('INNER JOIN', 'model m', 'm.id = d.model')
	    	->where(['like', new \yii\db\Expression('CAST(mac AS varchar)'), $q])
	    	->orWhere(['like', 'm.name', $q]);
	
	    	if(!is_null($type))
	    		$query->andWhere(['type' => $type]);
	
    		$command = $query->createCommand();
    		$data = $command->queryAll();
    		$out['results'] = array_values($data);
		}
		elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'concat' => Device::findOne($id)->modelAddress->fullDeviceAddress];
		}
		return $out;
	}

    /**
     * Finds the Modyfication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Modyfication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
	
	public function actionValidation($type){
		 
		$modelDevice = DeviceFactory::create($type);
		
		$request = Yii::$app->request;
		
		if ($request->isAjax && $modelDevice->load($request->post())) {
			
				var_dump($modelDevice); exit();
	           	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	               	return ActiveForm::validate($modelDevice, 'mac');

              	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($modelDevice, 'serial');

		}
	}
	
    protected function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
