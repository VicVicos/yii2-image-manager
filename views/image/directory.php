<?php

/* @var $model array */
/* @var $class string */

?>

<li class="item j-directory-item <?= isset($class) ? $class : '' ?>" data-path="<?= trim($model['path'], DIRECTORY_SEPARATOR) ?>">
	<span class="glyphicon glyphicon-folder-open directory j-directory-toggle"></span>
	<span class="j-directory name"><?= $model['name'] ?></span>
</li>
