<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class KioskAsset extends AssetBundle {
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $css = [
		'css/all.css',
		'css/keyboard.css',
		'css/kiosk.css',
        'css/datepicker/jquery-ui.css',
        'css/swiper/swiper.css',
        'css/swiper/swiper.min.css',
//        '//unpkg.com/swiper/css/swiper.css',
//        '//unpkg.com/swiper/css/swiper.min.css',
	];
	public $js = [
        'js/swiper/swiper.js',
        'js/swiper/swiper.min.js',
//        'js/swiper/swiper.esm.js',
//        '//unpkg.com/swiper/js/swiper.js',
//        '//unpkg.com/swiper/js/swiper.min.js',
		'js/paginathing.js',
		'js/axios.min.js',
		'js/jquery.mask.min.js',
		'js/keyboard.js',
		'js/layout.js',
//        'js/ifvisible.min.js',
		'js/jquery.printPage.js',

        'js/datepicker/jquery-ui.js',
        'js/datepicker/datepicker-ru.js',
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapPluginAsset',
	];
}
