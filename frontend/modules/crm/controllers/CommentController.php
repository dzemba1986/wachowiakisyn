<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\Comment;
use common\models\crm\Task;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Exception;

class CommentController extends Controller {
	
    public function actionIndex($taskId) {
        
        $comments = Comment::find()->joinWith('createBy')->select([
            'create_at', 'create_by', 'last_name', 'desc',
        ])->where(['task_id' => $taskId])->orderBy('create_at')->asArray()->all();
    	
    	return $this->renderAjax('index', [
    		'comments' => $comments
    	]);
    }

    public function actionCreate($taskId) {
    	
    	$request = \Yii::$app->request;
    	$comment = new Comment();
    	
    	if ($comment->load($request->post())) {
    	    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    	$task = Task::findOne($taskId);
//             $task->status = 2;
    		$comment->task_id = $taskId;
    		
    		try {
	    	  if (!($comment->save() && $task->save())) throw new Exception('Nie dodano komentarza');
    		} catch (Exception $e) {
    		    return [0, $e->getMessage()];
    		}
    		
    		return [1, 'Dodano komentarz'];
    	} else {
	    	return $this->renderAjax('create', [
	    		'comment' => $comment
	    	]);
    	}
    }
    
    public function actionDelete($id) {
    	
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }

    protected function findModel($id) {
    	
        if ($model = Comment::findOne($id) !== null) return $model;
        else throw new NotFoundHttpException('The requested page does not exist.');
    }
}