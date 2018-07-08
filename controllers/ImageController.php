<?php

namespace vicos\imagemanager\controllers;

use finfo;
use himiklab\thumbnail\EasyThumbnailImage;
use InvalidArgumentException;
use vicos\imagemanager\ImageManager;
use yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * REST ImageController управляет файлами и директориями
 */
class ImageController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['access'] = [
			'class' => AccessControl::className(),
			'rules' => [
				[
					'allow' => true,
					'roles' => ['@'],
				],
			]
		];
		return $behaviors;
	}

	/**
	 * Возвращает шаблон с директориями и изображениями
	 *
	 * @return array
	 */
	public function actionIndex()
	{
		$baseDirectory = $this->getBaseDirectory();
		$selectedDirectory = [
			'name' => $baseDirectory,
			'path' => $baseDirectory,
			'items' => []
		];
		
		return $this->renderPartial('index', [
			'selectedDirectory' => $selectedDirectory,
			'directories' => $this->getDirectories($baseDirectory),
			'images' => $this->getFiles($baseDirectory),
		]);
	}

	/**
	 * Возвращает список файлов заданной директории
	 *
	 * @param string $directory
	 * @return array
	 */
	public function actionImages($directory)
	{
		return $this->getFiles($directory);
	}

	/**
	 * Возвращает рекурсивный список директорий заданной директории
	 *
	 * @param string $directory
	 * @return array
	 */
	public function actionDirectories($directory)
	{
		$directoryItem = [
			'name' => $directory,
			'path' => $directory,
			'items' => []
		];
		return [
			'directory' => $directoryItem,
			'list' => $this->getDirectories($directory)
		];
	}

	/**
	 * Создаёт папку $folderName в директории $directory
	 *
	 * @param string $directory
	 * @param string $folderName
	 * @return bool|string
	 */
	public function actionCreate($directory, $folderName)
	{
		if (!file_exists($directory . DIRECTORY_SEPARATOR . $folderName)) {
			mkdir($directory . DIRECTORY_SEPARATOR . $folderName);
			return $this->renderPartial('directory', [
				'model' => [
					'name' => $folderName,
					'path' => $directory . DIRECTORY_SEPARATOR . $folderName,
					'items' => []
				]
			]);
		} else {
			return false;
		}
	}

	/**
	 * Создаёт шаблон новой папки
	 * 
	 * @param string $directory
	 * @param string $folderName
	 * @return string
	 */
	public function actionCreateTemplate($directory, $folderName = 'Новая папка')
	{
		return $this->renderPartial('directory-template', [
			'name' => $folderName,
			'path' => $directory,
		]);
	}

	/**
	 * Удаляет файл $filename
	 *
	 * @param string $filename
	 * @return bool
	 * @throws NotFoundHttpException если файл не найден или не является файлом
	 */
	public function actionDeleteImage($filename)
	{
		if (file_exists($filename) && is_file($filename)) {
			unlink($filename);
			return true;
		} else {
			throw new NotFoundHttpException("Файл $filename не существует.");
		}
	}

	/**
	 * Удаляет рекурсивно директорию $folder
	 *
	 * @param string $folder
	 * @return bool
	 */
	public function actionDeleteDirectory($folder)
	{
		FileHelper::removeDirectory($folder);
		return true;
	}

	/**
	 * Загружает изображение из $_FILES['file'] в заданную директорию
	 * Возврвщает данные о изображении
	 *
	 * @param string $directory
	 * @return array
	 */
	public function actionUpload($directory)
	{
		$file = UploadedFile::getInstanceByName('file');
		if ($file !== null) {
			$filename = md5(time() . $file->tempName) . '.' . $file->extension;
			$file->saveAs($directory . DIRECTORY_SEPARATOR . $filename);
			$pathFile = $directory . DIRECTORY_SEPARATOR . $filename;
			$image = [
				'file' => $pathFile,
				'name' => mb_substr($filename, 0, 18),
				'size' => ImageManager::formatBytesRu(filesize($pathFile)),
				'preview' => EasyThumbnailImage::thumbnailFileUrl($pathFile, 132, 95, EasyThumbnailImage::THUMBNAIL_OUTBOUND),
			];
			$image['html'] = $this->renderPartial('image', ['model' => $image]);
			return $image;
		}
		return false;
	}

	/**
	 * Возвращает спиок файлов с информацией
	 *
	 * @param string $directory
	 * @return array
	 */
	protected function getFiles($directory)
	{
		if (realpath($directory) === false) {
			throw new InvalidArgumentException("Директории $directory не существует.");
		}

		/* @var $dir \Directory */
		$images = [];
		$dir = dir($directory);
		while (($file = $dir->read()) !== false) {
			if ($file !== '.' && $file !== '..') {
				$pathFile = $directory . DIRECTORY_SEPARATOR . $file;
				if (is_file($pathFile)) {
					$info = new Finfo(FILEINFO_MIME);
					$mime = $info->buffer(file_get_contents($pathFile));;
					if (preg_match('/image\/*/', $mime)) {
						$image = [
							'file' => $pathFile,
							'name' => mb_substr($file, 0, 18),
							'size' => ImageManager::formatBytesRu(filesize($pathFile)),
							'preview' => EasyThumbnailImage::thumbnailFileUrl($pathFile, 135, 95, EasyThumbnailImage::THUMBNAIL_OUTBOUND),
						];
						$image['html'] = $this->renderPartial('image', ['model' => $image]);
						$images[] = $image;
					}
				}

			}
		}

		return $images;
	}

	/**
	 * Возвращает имя базовой директории
	 * 
	 * @return string
	 */
	protected function getBaseDirectory()
	{
		$manager = Yii::$app->modules['imageManager'];
		if (is_array($manager)) {
			return isset($manager['directoryRoot']) ? $manager['directoryRoot'] : 'uploads';
		} else {
			return $manager->directoryRoot;
		}
	}

	/**
	 * Возвращает спиок файлов с информацией
	 *
	 * @param string $directory
	 * @return array
	 */
	protected function getDirectories($directory)
	{
		if (realpath($directory) === false) {
			throw new InvalidArgumentException("Директории $directory не существует.");
		}

		/* @var $dir \Directory */
		$directories = [];
		$dir = dir($directory);
		while (($file = $dir->read()) !== false) {
			if ($file !== '.' && $file !== '..') {
				$pathFile = $directory . DIRECTORY_SEPARATOR . $file;
				if (is_dir($pathFile)) {
					$directoryItem = [
						'name' => $file,
						'path' => $pathFile,
						'items' => []
					];
					$directoryItem['items'] = $this->getDirectories($pathFile);
					$directories[] = $directoryItem;
				}

			}
		}

		return $directories;
	}
}
