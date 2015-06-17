<?php
namespace app\modules\bpm\assets;

use yii\web\AssetBundle;

class IndexCreateAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	public $js = [
			'js/bpm/workflow/indexCreate.js',
			'js/bpm/workflow/bpm-i18n.js',
	];
	public $css = [
			'css/dialogWithoutClose.css',
	];
	public $depends = [
			'yii\web\JqueryAsset',
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
}