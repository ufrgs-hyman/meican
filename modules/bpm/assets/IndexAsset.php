<?php
namespace meican\bpm\assets;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
	public $sourcePath = '@meican/bpm/assets/public';
    
	public $js = [
		'index.js',
		'bpm-i18n.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
	
	public $jsOptions = ['position' => \yii\web\View::POS_END];
}