<?php
use yii\bootstrap\Tabs;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var integer $id Virtual ID
 */

echo Tabs::widget([
		'encodeLabels' => false,
		'items'=> [
		    [
		        'label' => 'Dane',
		    	'active' => true,	
				'linkOptions' => ['data-url' => Url::to(['virtual/update', 'id' => $id])]
		    ],
			[
				'label' => 'Adresacja',
				'linkOptions' => ['data-url' => Url::to(['ip/update', 'deviceId' => $id])]
			],
		]
		
]);

?>
<style>

/* custom inclusion of right, left and below tabs */

.tabs-left > .nav-tabs {
  border-bottom: 0;
}

.tab-content > .tab-pane,
.pill-content > .pill-pane {
  display: none;
}

.tab-content > .active,
.pill-content > .active {
  display: block;
}

.tabs-below > .nav-tabs {
  border-top: 1px solid #ddd;
}

.tabs-below > .nav-tabs > li {
  margin-top: -1px;
  margin-bottom: 0;
}

.tabs-below > .nav-tabs > li > a {
  -webkit-border-radius: 0 0 4px 4px;
     -moz-border-radius: 0 0 4px 4px;
          border-radius: 0 0 4px 4px;
}

.tabs-below > .nav-tabs > li > a:hover,
.tabs-below > .nav-tabs > li > a:focus {
  border-top-color: #ddd;
  border-bottom-color: transparent;
}

.tabs-below > .nav-tabs > .active > a,
.tabs-below > .nav-tabs > .active > a:hover,
.tabs-below > .nav-tabs > .active > a:focus {
  border-color: transparent #ddd #ddd #ddd;
}

.tabs-left > .nav-tabs > li {
  float: none;
}

.tabs-left > .nav-tabs > li > a {
  min-width: 74px;
  margin-right: 0;
  margin-bottom: 3px;
}

.tabs-left > .nav-tabs {
  float: left;
  margin-right: 19px;
  border-right: 1px solid #ddd;
}

.tabs-left > .nav-tabs > li > a {
  margin-right: -1px;
  -webkit-border-radius: 4px 0 0 4px;
     -moz-border-radius: 4px 0 0 4px;
          border-radius: 4px 0 0 4px;
}

.tabs-left > .nav-tabs > li > a:hover,
.tabs-left > .nav-tabs > li > a:focus {
  border-color: #eeeeee #dddddd #eeeeee #eeeeee;
}

.tabs-left > .nav-tabs .active > a,
.tabs-left > .nav-tabs .active > a:hover,
.tabs-left > .nav-tabs .active > a:focus {
  border-color: #ddd transparent #ddd #ddd;
  *border-right-color: #ffffff;
}

</style>


<script>

$(function(){

	$("#w0-tab0").load($("a[href='#w0-tab0']").attr('data-url'))

	$('a[data-toggle="tab"]').click(function(e) {
	    var $this = $(this),
	        loadurl = $this.attr('data-url'),
	        targ = $this.attr('href');

	    $(targ).load(loadurl);

	    $this.tab('show');
	    return false;
	});
	
});

</script>