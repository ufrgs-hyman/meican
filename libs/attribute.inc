<?php
class Attribute {
	var $type;
	var $name;
	var $primaryKey;
	var $usedInInsert;
	var $usedInUpdate;
        var $forceUpdate;

	function Attribute($name, $type, $primaryKey, $usedInInsert, $usedInUpdate, $forceUpdate) {
		$this->name = $name;
		$this->type = $type;
		$this->primaryKey = $primaryKey;
		$this->usedInInsert = $usedInInsert;
		$this->usedInUpdate = $usedInUpdate;
                $this->forceUpdate = $forceUpdate;
	}
}
?>
