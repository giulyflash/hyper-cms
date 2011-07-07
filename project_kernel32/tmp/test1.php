<?php
class test1{
	private $param1 = 0;
	
	public function set_var($name,$value){
		$this->{$name} = $value;
	}
	
	
	public function get_var($name){
		return $this->$name;
	}
	
	public function __set($key, $value)
    {
        $this->$key = $value;
    }
	
	public function get_vars(){
		$vars = get_object_vars($this);
		$output = array();
		foreach($vars as $name=>$value)
			if(substr($name, 0, 1)!='_')
				$output[$name] = $value;
		return $output;
	}
}

$test = new test1();
$test->set_var('param1', '1');
$param2name = 'param2';
$test->$param2name = 2;
$test->set_var('param3', '3');
//var_dump(get_object_vars($test));
//var_dump($test->get_vars());
var_dump($test->get_vars(), get_object_vars($test));

?>