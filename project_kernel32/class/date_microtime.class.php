<?php
class date_microtime extends DateTime{
	private $microtime;
	
	private $delimiter = '.';
	
	private $count = 4;
	
	public function __construct(){
		echo(parent::__construct());
		$this->microtime = microtime(true);
	}
	
	public function format($format = 'Y-m-d H:i:s'){
		return parent::format($format).$this->delimiter.$this->fract($this->microtime);
	}
	
	private function fract($var){
		$fract = (string)(floor( ($var - floor($var)) * pow(10,$this->count)));
		while(strlen($fract) < $this->count){
			$fract = '0'.$fract;
		}
		return $fract;
	}
}
?>