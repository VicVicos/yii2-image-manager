<?php

/* @var $this yii\web\View */
/* @var $directories array */
/* @var $selectedDirectory array */
/* @var $images array */

use vicos\imagemanager\ImageManager;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= Html::hiddenInput('', Url::to(['/imageManager/image/images', 'directory' => '-dir-']), ['id' => 'image-manager-images']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/directories', 'directory' => '-dir-']), ['id' => 'image-manager-folders']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/create', 'directory' => '-dir-', 'folderName' => '-folder-']), ['id' => 'image-manager-create']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/create-template', 'directory' => '-dir-', 'folderName' => '-folder-']), ['id' => 'image-manager-create-template']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/delete-image', 'filename' => '-filename-']), ['id' => 'image-manager-delete-image']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/delete-directory', 'folder' => '-folder-']), ['id' => 'image-manager-delete-directory']) ?>
<?= Html::hiddenInput('', Url::to(['/imageManager/image/upload', 'directory' => '-dir-']), ['id' => 'image-manager-upload']) ?>
<?= Html::hiddenInput('', null, ['id' => 'image-manager-selected-image']) ?>

<div class="image-manager">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Директории</h3>
				</div>
				<div class="panel-body">
					<ul class="tree list-folders">
						<?= $this->render('directory', ['model' => $selectedDirectory, 'class' => 'active']) ?>
						<?= ImageManager::renderFolders($directories) ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Изображения</h3>
				</div>
				<div class="panel-body drag-zone drag-all">
					<ul class="list-inline list-images">
						<?php foreach ($images as $image) : ?>
							<?= $this->render('image', ['model' => $image]) ?>
						<?php endforeach; ?>
					</ul>
					<div class="drag-zone">
						<input type="file" name="file" multiple="multiple">
					</div>
					<span class="upload-percentage alert alert-success">
						<span class="value">0%</span>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>