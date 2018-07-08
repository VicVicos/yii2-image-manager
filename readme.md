# Yii2 Image Manager
Расширение для управления изображениями для редактора Imperavi [https://github.com/vova07/yii2-imperavi-widget](https://github.com/vova07/yii2-imperavi-widget).
Перводим на git

## Установка
Выполнить команду в консоли
```bash
composer require --dev --prefer-dist vicos/yii2-image-manager
```

или добавить в composer.json
```php
"require-dev": {
    "vicos/yii2-image-manager": "@dev"
}
```

## Использование
Добавить подключение модуля `yii2-image-manager` в конфигурацию приложения.
```php
'modules' => [
    'imageManager' => [
        'class' => 'vicos\imagemanager\ImageManager',
        'directoryRoot' => 'uploads'
    ]
]
```

Добавить в вызов виджета
```php
use vova07\imperavi\Widget;
use vicos\imagemanager\ImageManagerAsset;

<?= $form->field($model, 'username')->widget(Widget::className(), [
    'settings' => [
        'lang' => 'ru',
        'minHeight' => 400,
    ],
    'plugins' => [
        'imageManager' => ImageManagerAsset::className(),
    ]
]); ?>
```
