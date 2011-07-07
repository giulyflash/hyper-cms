<?php
class test_class{
	function test_func($arg1, $arg2){
		echo __FUNCTION__.', '.__METHOD__.'<br/>';
		var_dump(func_get_args());
		echo "<br/>";
		var_dump($arg1, $arg2);
	}
}

$test_obj = new test_class();
call_user_func_array(array($test_obj,'test_func'),array('a','b','c'));
unset($test_obj);
unset($qwerty_asdfg);
?>