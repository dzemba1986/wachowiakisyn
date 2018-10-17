<?php


/**
 * @var yii\web\View $this
 * @var common\models\crm\Comment $comments
 */
?>
<div class="comment-index">

	<?php foreach ($comments as $comment): ?>

		<div class="col">
			<h4><p><?= $comment->description ?></p></h4>
			<p>
				<?= $comment->create ?>
				<tab style="padding-left: 4em;"><?= $comment->user->last_name ?></tab>
			</p>
			<hr>
		</div>
		
	<?php endforeach; ?>
	
</div>

<?php
$js = <<<JS
$(function() {
    $('.modal-header h4').html('Komentarze');
});
JS;

$this->registerJs($js);
?>