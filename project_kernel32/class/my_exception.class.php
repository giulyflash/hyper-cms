<?php
class my_exception extends Exception{
	public $text;
	public $lvl;
	public $parent;
	public function __construct($long_code, $text=NULL, $lvl=NULL, $need_backtrace=NULL){
		//$long_code - text of error in English used as error code and outputed when language definition not found
		//$text - text of error
		//TODO $text as array. When array str_replace arrray_key by array_value in language definition of error
		//TODO auto get_language from language file in __construct
		//$lvl - level of error in debug backtrace 
		parent::__construct($long_code);
		$this->need_backtrace = $need_backtrace;
		$this->text = $text;
		$this->lvl = $lvl;
	} 
}
?>