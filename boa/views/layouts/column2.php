<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Menu;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@app/views/layouts/main.php');
        ?>

        <div class="container">
	        <div class="col-xs-2 sidebar">
				<?php echo Menu::widget([
					'items'=>isset($this->params['menu']) ? $this->params['menu'] : [],
					'submenuTemplate' => "\n<ul class='dropdown-menu' role='menu'>\n{items}\n</ul>\n",
					//'options' => ['class' => 'recent-posts'],
					//'header' => 'Recent Posts',
					//'body' => app\widgets\RecentPosts::widget()
				]); ?>
			</div>
	        
	        <div class="col-xs-9">
				<?= $content; ?>
			</div>
		
        </div>
<?php $this->endContent(); ?>