<?php

namespace vicos\imagemanager\assets;

use yii\web\AssetBundle;

class ImageManagerAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@vendor/vicos/yii2-image-manager/assets/';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'css/imperavi-plugins.css',
		'css/jquery.contextMenu.css',
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'js/jquery.contextMenu.js',
		'js/imageManager.js',
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];
}
