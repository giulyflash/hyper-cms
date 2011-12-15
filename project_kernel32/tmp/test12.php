<?php
$pattern = '%^([^\.\?/]+)/(\?.*)?$%';
$string = 'my_title/?_qerty=1';
var_dump(preg_replace($pattern, '/?call=article.get_category&id=$1$2', $string));
// string(49) "/?call=article.get_category&id=my_title/?_qerty=1"
?>