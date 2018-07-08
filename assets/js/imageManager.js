/**
 * ImageManager Plugin позволяет управлять изображениями. Так же позволяет управлять директориями изображений.
 * Drag and Drop загрузка файлов, валидация.
 *
 * @author sergKs <serg31ks@yandex.ru>
 */

if (!RedactorPlugins) var RedactorPlugins = {};

$(document).ready(function ($) {
	/**
	 * Количество всех файлов для загрузки
	 * @type {number}
	 */
	var countAll = 0;

	/**
	 * Текущее количество загруженный файлов
	 * @type {number}
	 */
	var count = 0;

	/**
	 * Процент загрузки
	 * @type {number}
	 */
	var completePercentage = 0;

	/**
	 * Объект анимации загрузки файла
	 * @type {Object}
	 */
	var loading = null;

	/**
	 * Загружает файл в указанную директорию
	 * @param directory
	 * @param file
	 */
	function uploadFile(directory, file) {
		var xhr = new XMLHttpRequest();
		var drag = $('.image-manager .drag-all')[0];
		xhr.onload = function () {
			if (this.status === 200) {
				var data = JSON.parse(this.responseText);
				$('.image-manager .list-images').append(data.html);
				count++;
				if (count === countAll) {
					initCallbacksImages();
					loading.fadeOut();
					drag.scrollTop = drag.scrollHeight;
				}
			}
		};
		xhr.onprogress = function (event) {
			var complete = Math.ceil(count * (event.loaded * 100 / event.total) / countAll);
			completePercentage.html(complete + '%');
		};

		var data = new FormData();
		data.append('file', file);
		xhr.open('POST', directory, true);
		xhr.send(data);
	}

	/**
	 * Иницилизация обработчиков для загрузки файлов
	 */
	function initCallbacksUpload() {
		var dropZone = $('.image-manager .drag-zone');
		var input = $('.image-manager .drag-zone input');
		var uploadUrl = $('#image-manager-upload').val();
		loading = $('.image-manager .upload-percentage');
		completePercentage = $('.image-manager .upload-percentage .value');

		dropZone.on('dragover', function () {return false;});
		dropZone.on('dragleave', function () {return false;});

		dropZone.on('drop', function (event) {
			event.preventDefault();
			var directory = $('.image-manager .list-folders .j-directory-item.active').attr('data-path');
			var files = event.originalEvent.dataTransfer.files;
			countAll = files.length;
			if (countAll > 0) {
				count = 0;
				loading.show();
				for (var i = 0; i < countAll; i++) {
					uploadFile(uploadUrl.replace('-dir-', directory), files[i]);
				}
			}
		});

		input.on('change', function (event) {
			event.preventDefault();
			var directory = $('.image-manager .list-folders .j-directory-item.active').attr('data-path');
			var files = event.target.files;
			countAll = files.length;
			if (countAll > 0) {
				count = 0;
				loading.show();
				for (var i = 0; i < countAll; i++) {
					uploadFile(uploadUrl.replace('-dir-', directory), files[i]);
				}
			}
		});
	}

	/**
	 * Инициализация обработчиков для изображений
	 */
	function initCallbacksImages() {
		var image = $('.j-image');

		image.on('click', function () {
			var item = $(this);
			$('#image-manager-selected-image').val(item.attr('data-path'));
			$('.j-image').removeClass('active');
			item.addClass('active');
		});

		image.on('dblclick', function () {
			var item = $(this);
			$('#image-manager-selected-image').val(item.attr('data-path'));
			$('.j-image').removeClass('active');
			item.addClass('active');
			$('.redactor-modal-action-btn').click();
		});

		$.contextMenu({
			selector: '.j-image',
			items: {
				'delete': {
					name: 'Удалить', icon: 'delete', callback: function (key, options) {
						var item = options.$trigger;
						var url = $('#image-manager-delete-image').val().replace('-filename-', item.attr('data-path'));
						$.ajax({
							type: 'POST',
							url: url,
							contentType: 'json',
							success: function () {
								item.remove();
							}
						});
					}
				}
			}
		});
	}

	/**
	 * Инициализация обработчиков для директорий
	 */
	function initCallbacksDirectories() {
		$('.j-directory').on('click', function () {
			var item = $(this).parents('li');
			var dir = item.attr('data-path');
			var listImages = $('.image-manager .list-images');
			var url = $('#image-manager-images').val().replace('-dir-', dir);

			$('.j-directory-item').removeClass('active');
			item.addClass('active');
			$.ajax({
				type: 'GET',
				url: url,
				contentType: 'json',
				success: function (data) {
					listImages.html('');
					$('#image-manager-selected-image').val('');
					data.forEach(function (item) {
						listImages.append(item.html);
					});
					initCallbacksImages();
				}
			});
		});

		$('.j-directory-toggle').on('click', function () {
			var next = $(this).parents('li').next();
			if (next.hasClass('tree') && next.length > 0) {
				next.slideToggle();
			}
		});

		$.contextMenu({
			selector: '.j-directory',
			items: {
				'paste': {
					name: 'Добавить папку', icon: 'paste', callback: function (key, options) {
						var item = options.$trigger.parents('li');
						var url = $('#image-manager-create-template').val().replace(
							'-dir-',
							item.attr('data-path')
						).replace('-folder-', 'Новая папка');
						$.ajax({
							type: 'POST',
							url: url,
							contentType: 'json',
							success: function (data) {
								var next = item.next();
								if (next.hasClass('tree')) {
									next.append(data)
								} else {
									item.after('<ul class="tree">' + data + '</ul>');
								}
								var input = $('#image-manager-new-directory');
								input.val('Новая папка');
								input.select();
								input.focus();
								initCallbackInput();
							}
						});
					}
				},
				'delete': {
					name: 'Удалить', icon: 'delete', callback: function (key, options) {
						var item = options.$trigger.parents('li');
						var url = $('#image-manager-delete-directory').val().replace(
							'-folder-',
							item.attr('data-path')
						);
						$.ajax({
							type: 'POST',
							url: url,
							contentType: 'json',
							success: function () {
								var next = item.next();
								if (next.hasClass('tree')) {
									next.remove();
								}
								item.remove();
								$('.image-manager .list-folders .j-directory:last').click();
							}
						});
					}
				}
			}
		});
	}

	/**
	 * Инициализация обработчиков для создания нововй папки
	 */
	function initCallbackInput() {
		var newDirectory = $('#image-manager-new-directory');

		newDirectory.on('focusout', function () {
			var item = $(this).parents('li');
			var text = $(this).val().trim();
			var url = $('#image-manager-create').val().replace(
				'-dir-',
				item.attr('data-path')
			).replace('-folder-', text);
			if (text.length > 0) {
				$.ajax({
					type: 'POST',
					url: url,
					contentType: 'json',
					success: function (data) {
						if (data !== false) {
							item[0].outerHTML = data;
							initCallbacksDirectories();
						} else {
							alert('Директория "' + text + '" уже существует.');
							$('#image-manager-new-directory').focus();
						}
					}
				});
			} else {
				item.remove();
			}
		});

		newDirectory.on('keydown', function (e) {
			if (e.keyCode === 13) {
				var item = $(this).parents('li');
				var text = $(this).val().trim();
				var url = $('#image-manager-create').val().replace(
					'-dir-',
					item.attr('data-path')
				).replace('-folder-', text);
				if (text.length > 0) {
					$.ajax({
						type: 'POST',
						url: url,
						contentType: 'json',
						success: function (data) {
							if (data !== false) {
								$('#image-manager-new-directory').remove();
								item[0].outerHTML = data;
								initCallbacksDirectories();
							} else {
								alert('Директория "' + text + '" уже существует.');
								$('#image-manager-new-directory').focus();
							}
						}
					});
				} else {
					item.remove();
				}
			}
		})
	}

	/**
	 * Плагин редактора
	 * @returns {{init: init, show: show, insert: insert}}
	 */
	RedactorPlugins.imageManager = function () {
		return {
			init: function () {
				var button = this.button.add('imageManager', 'Управление изображениями');
				this.button.addCallback(button, this.imageManager.show);
			},
			show: function () {
				var item = this;
				$.ajax({
					type: 'GET',
					url: '/imageManager/image/index',
					contentType: 'json',
					success: function (data) {
						item.modal.addTemplate('imageManager', data);
						item.modal.load('imageManager', 'Управление изображениями', 1350);
						item.modal.createCancelButton();
						var button = item.modal.createActionButton('Добавить');
						button.on('click', item.imageManager.insert);
						item.selection.save();
						item.modal.show();
						initCallbacksDirectories();
						initCallbacksImages();
						initCallbacksUpload();
					}
				});
			},
			insert: function () {
				this.modal.close();
				var url = $('#image-manager-selected-image').val();
				if (url.length > 0) {
					var html = '<img src="/' + url + '">';
					this.insert.html(html);
					this.observe.images();
				}
			}
		};
	};
});