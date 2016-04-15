<?php
namespace meican\bpm\assets;

use yii\web\AssetBundle;

class IndexCreateAsset extends AssetBundle
{
	public $sourcePath = '@meican/bpm/assets/public';
	
	public $js = [
			'indexCreate.js',
			'bpm-i18n.js',
	];
	
	public $depends = [
			'yii\web\JqueryAsset',
	];
	
	public $jsOptions = ['position' => \yii\web\View::POS_END];
}