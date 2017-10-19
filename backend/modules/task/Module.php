<?php

namespace backend\modules\task;

use Yii;

/**
 * Class Module
 *
 * @package task
 */
class Module extends \yii\base\Module
{
	/**
	 * @var string the class name of the [[identity]] object
	 */
	public $userIdentityClass;
	
	/**
	 * @var string the class name of the comment model object, by default its yii2mod\comments\models\CommentModel
	 */
	public $taskModelClass = 'backend\modules\task\models\Task';
	
	/**
	 * @var string the namespace that controller classes are in
	 */
	public $controllerNamespace = 'backend\modules\task\controllers';
	
	/**
	 * @var bool when admin can edit comments on frontend
	 */
	public $enableInlineEdit = false;
	
	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
		
		if ($this->userIdentityClass === null) {
			$this->userIdentityClass = Yii::$app->getUser()->identityClass;
		}
	}
}
