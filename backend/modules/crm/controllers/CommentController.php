<?php

namespace backend\modules\crm\controllers;

use common\models\crm\models\Comment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CommentController extends Controller{
	
    public function actionIndex($taskId){
        
    	$comments = Comment::find()->where(['task_id' => $taskId])->orderBy('create')->all();
    	
    	return $this->renderAjax('index', [
    		'comments' => $comments
    	]);
    }

    public function actionCreate($taskId){
    	
    	$request = \Yii::$app->request;
    	
    	if ($request->isAjax){
	    	$comment = new Comment();
	    	
	    	if ($comment->load($request->post())){
		    	
	    		$comment->task_id = $taskId;
		    	
		    	if ($comment->save())
		    		return 1;
		    	else {
		    		print_r($comment->errors);
		    		return 0;
		    	}
	    	} else {
		    	return $this->renderAjax('create', [
		    		'comment' => $comment
		    	]);
	    	}
    	}
    }
    
    public function actionUpdate($id){
    	
    }
    
    public function actionDelete($id){
    	
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }

    protected function findModel($id){
    	
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}