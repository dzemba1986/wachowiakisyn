<?php

use backend\modules\task\models\Comment;
use yii\web\View;

/**
 * @var View $this
 * @var Comment $comment
 */
?>
<div class="comment-index">

	<?php foreach ($comments as $comment): ?>
	
		<div class="col">
			<h4><p><?= $comment->description ?></p></h4>
			<p><?= $comment->user->last_name ?></p>
			<hr>
		</div>
		
		
	<?php endforeach;?>
	
</div>