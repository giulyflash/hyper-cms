<?php
	$str = '12345';
	var_dump(
		$str,
		substr($str, 0, strlen($str)),
		substr($str, 0, strlen($str)-1),
		substr($str, 0, strlen($str)-2),
		substr($str, 1, strlen($str)),
		substr($str, 1, strlen($str)-1),
		substr($str, 1, strlen($str)-2)
	);
?>