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
class Index extends AssetBundle
{
	public $sourcePath = '@meican/bpm/assets/public';
    
	public $js = [
		'index.js',
	];
	public $depends = [
		'meican\base\assets\ToggleAsset',
	];
}