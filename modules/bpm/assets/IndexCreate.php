<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\bpm\assets;

use yii\web\AssetBundle;

/**
 * @author Diego Pittol
 */
class IndexCreate extends AssetBundle
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