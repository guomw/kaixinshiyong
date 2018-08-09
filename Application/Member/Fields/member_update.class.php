<?php
class member_update {
	var $modelid;
	var $fields;
	var $data;

    function __construct($modelid) {
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid, 'model');
    }
}?>