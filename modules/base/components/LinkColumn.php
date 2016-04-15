<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\components;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\Column;

/**
 * @author Henrique Resende
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class LinkColumn extends Column
{
    public $href = '#';
    public $image;
    public $label = "";
    public $text;
	public $url;
	public $title = '';
	public $sendId = true;
    
    protected function renderHeaderCellContent(){
    	if(isset($this->label))
    		return Html::encode($this->label);
    	else
    		return 'Link';
    }
    
    protected function renderDataCellContent($model, $key, $index) {
    	if(isset($this->image)){
    		$imageTag = Html::img('@web'.$this->image, ['title'=>$this->title]);
    		if($this->sendId) $href = Url::toRoute([$this->url, 'id'=>$key]);
    		else $href = Url::toRoute([$this->url]);
    		$aTag = Html::a($imageTag, $href);
    		return $aTag;
    	}else if(isset($this->text)){
    		$textEncoded = Html::encode($this->text);
    		if($this->sendId) $href = Url::toRoute([$this->url, 'id'=>$key]);
    		else $href = Url::toRoute([$this->url]);
    		$aTag = Html::a($this->text, $href);
    		
    		return $aTag;
    	}else{
    		return '#Error';
    	}
    }

}
