<?php

/* @var $model array */

use yii\helpers\Html;

?>

<li class="j-image img-thumbnail" data-path="<?= $model['file'] ?>">
	<div class="img"><?= Html::img($model['preview']) ?></div>
	<div class="info">
		<div><small><strong><?= $model['name'] ?></small></strong></div>
		<div><small><?= $model['size'] ?></small></div>
	</div>
</li>