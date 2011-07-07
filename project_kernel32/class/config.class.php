<?php
class config{
	public function __construct($array = null, $parent = NULL){
		$this->set_vars($array);
	}
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function get($name=NULL, $reference=NULL){
		if(!$name)throw new my_exception('variable name not found');
		//TODO level for all errors
		return @$this->$name;
	}
	
	public function &get_ref($name=NULL){
		if(!$name)throw new my_exception('variable name not found');
		//@$ref = &$this->$name; return $ref;
		return $this->$name;
	}
	
	public function set($name=NULL, $value=NULL){
		if(!$name)
			throw new my_exception('variable name not found');
		$this->$name = $value;
	}
	
	public function set_ref($name=NULL, &$value=NULL){
		if(!$name)
			throw new my_exception('variable name not found');
		$this->$name = &$value;
	}
	
	public function get_vars(){
		return get_object_vars($this);
	}
	
	public function set_vars($array){
		if($array){
			foreach($array as $name=>$value)
				$this->$name = $value;
		}
	}
} 
?>