<?php
//die;
/*$array = array('a','b','c','d','e');
foreach($array as $num=>&$item){
	execute($array, $num, $item);
}

function execute(&$array, $num, &$item){
	echo $item;
	if($num<10)
		$array[] = $item.$item;
}

var_dump($array);*/
$array1 = array(
	1=>'a',
	2=>'b',
);
$array2 = array(
	1=>'c',
	2=>'d',
);
var_dump(array_merge($array1, $array2));
?>