# Yii2 Image Manager
Расширение для управления изображениями для редактора Imperavi [https://github.com/vova07/yii2-imperavi-widget](https://github.com/vova07/yii2-imperavi-widget).

## Установка
Выполнить команду в консоли
```bash
composer require --dev --prefer-dist sergks/yii2-image-manager
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
        'class' => 'sergks\imagemanager\ImageManager',
        'directoryRoot' => 'uploads'
    ]
]
```

Добавить в вызов виджета
```php
use vova07\imperavi\Widget;
use sergks\imagemanager\ImageManagerAsset;

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