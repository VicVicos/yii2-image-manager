<?php

namespace sergks\imagemanager;

use yii;

/**
 * imageManager модуль для управления изображениями
 * 
 * @author sergKs <serg31ks@yandex.ru>
 */
class ImageManager extends yii\base\Module
{
	/**
	 * Базовая директория
	 * @var string
	 */
	public $directoryRoot = 'uploads';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * Выводит рекурсивно папки
	 * 
	 * @param array $folders
	 * @return string
	 */
	public static function renderFolders(array $folders)
	{
		if (count($folders) === 0) {
			return '';
		}
		$tree = '<ul class="tree">';
		foreach ($folders as $folder) {
			$tree .= Yii::$app->view->render('@vendor/sergks/yii2-image-manager/views/image/directory', ['model' => $folder]);
			$tree .= self::renderFolders($folder['items']);
		}
		$tree .= '</ul>';
		return $tree;
	}

	/**
	 * Форматирует количество байт.
	 *
	 * @param integer $bytes Количество байт; например, размер файла
	 * @return string Возвращает форматированное количество байт
	 * @author MaximAL
	 * @since 2016-03-17 Исправлена ошибка в условии цикла, из-за которой 999,9 МБ превращались в 1000 МБ.
	 * @since 2015-11-14 Добавлен неразрывный пробел.
	 * @since 2014-09-15 Первая версия.
	 * @date 2014-09-15
	 * @copyright © MaximAL, Sijeko 2014—2016
	 */
	public static function formatBytesRu($bytes)
	{
		/* @var string[] Единицы измерения: от байта до йоттабайта */
		static $units = ['Б', 'кБ', 'МБ', 'ГБ', 'ТБ', 'ПБ', 'ЭБ', 'ЗБ', 'ЙБ'];

		$index = 0;
		$bytes = 1.0 * $bytes;

		// Если поставить `>= 1000`, будет некорректно работать, например, на 999,9 МБ
		while ($bytes > 999) {
			$index++;
			$bytes /= 1024;
		}

		/** @var string $nbsp Неразрывный пробел */
		$nbsp = ' ';

		// Оставляем три значащих цифры и единицу измерения.
		// Если надо, убираем незначащие нули после запятой.
		if ($bytes > 99) {
			return number_format($bytes, 0, ',', $nbsp) . $nbsp . $units[$index];
		}

		if ($bytes > 9) {
			return str_replace(',0', '', number_format($bytes, 1, ',', ' ')) . $nbsp . $units[$index];
		}

		return str_replace(',00', '', number_format($bytes, 2, ',', ' ')) . $nbsp . $units[$index];
	}
}
