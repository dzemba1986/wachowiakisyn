<?php

namespace frontend\modules\seu\controllers\network;

use common\models\seu\network\DhcpValue;
use common\models\seu\network\Ip;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DhcpValueController extends Controller
{	
	public function actionUpdate($subnet)
	{
		$modelDhcpValues = !is_null($subnet) ? DhcpValue::find()->where(['subnet' => $subnet]) : [];
		
		$request = Yii::$app->request;
		
		if ($request->isAjax){
			if(!empty($request->post('network'))){
				$newModelIps = [];
				$index = 0;
				foreach ($request->post('network') as $net){
					$newModelIp = new Ip();
					$newModelIp->ip = $net['ip'];
					$newModelIp->subnet = $net['subnet'];
					$newModelIp->main = $index == 0 ? true : false;
					$newModelIp->device = $device;
					$index++;
					 
					$newModelIps[] = $newModelIp;
				}
				$i = 0;
		
				if(count($modelIps) == count($newModelIps)){
					for($i=0; $i <= count($modelIps)-1; $i++){
		
						//var_dump($modelIps[$i]->ip); exit();
		
						if($modelIps[$i]->ip == $newModelIps[$i]->ip){
							null; //brak zmian
						} else {
							$modelIps[$i]->ip = $newModelIps[$i]->ip;
							$modelIps[$i]->subnet = $newModelIps[$i]->subnet;
							$modelIps[$i]->main = $i == 0 ? true : false;
								
							try {
								if(!$modelIps[$i]->save())
									throw new Exception('Problem z zapisem adresu ip');
							} catch (Exception $e) {
								var_dump($modelIps[$i]->errors);
								var_dump($e->getMessage());
								exit();
							}
						}
					}
					return 1;
				} elseif (count($modelIps) > count($newModelIps)){
					 
					//var_dump($newModelIps); var_dump($modelIps); exit();
					 
					for($i=0; $i <= count($modelIps)-1; $i++){
						 
						if($i <= count($newModelIps)-1){
							if($modelIps[$i]->ip == $newModelIps[$i]->ip){
								null; //brak zmian
							} else {
								$modelIps[$i]->ip = $newModelIps[$i]->ip;
								$modelIps[$i]->subnet = $newModelIps[$i]->subnet;
								$modelIps[$i]->main = $i == 0 ? true : false;
								 
								try {
									if(!$modelIps[$i]->save())
										throw new Exception('Problem z zapisem adresu ip');
								} catch (Exception $e) {
									var_dump($modelIps[$i]->errors);
									var_dump($e->getMessage());
									exit();
								}
							}
						} else {
							$modelIps[$i]->delete();
						}
					}
					return 1;
				} elseif (count($modelIps) < count($newModelIps)){
					 
					//var_dump($newModelIps); var_dump($modelIps); exit();
					foreach ($modelIps as $modelIp)
						$modelIp->delete();
					 
					foreach ($newModelIps as $newModelIp){
								 
						try {
							if(!$newModelIp->save())
								throw new Exception('Problem z zapisem adresu ip');
						} catch (Exception $e) {
							var_dump($newModelIp->errors);
							var_dump($e->getMessage());
							exit();
						}
					}
				}
				return 1;
				//var_dump($newModelIps); exit();
			} else {
				return $this->renderAjax('update', [
					'modelDhcpValues' => $modelDhcpValues,
				]);
			}
		}
	}
    
    protected function findModel($id)
    {
        if (($model = DhcpValue::findOne($id)) !== null) {
        	return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
