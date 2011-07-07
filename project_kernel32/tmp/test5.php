<?php
//var_dump(getcwd());die;
require '/../class/my_exception.class.php';
require '/../class/object_sql_query.class.php';
try{
	$temp = array(1);
	$db_query = new object_sql_query($temp);
	//sintax sample:
	/*echo $db_query->insert('article')->values(array('title'=>1,'translit_title'=>2))->get_sql();
	echo '<br/>';
	echo $db_query->insert('article','title,translit_title')->values('1,2')->get_sql();
	echo '<br/>';
	$value = array(array(1,2),array(3,4));
	echo $db_query->insert('article','title,translit_title')->values($value)->get_sql();
	echo '<br/>';
	echo $db_query->delete()->from('article')->where('id',NULL)->get_sql();*/
	echo '<br/>';
	echo $db_query->update('article')->set(array('title'=>1,'translit_title'=>NULL))->get_sql();
	echo '<br/>';
	$value = array ( 'title' => 'О компании', 'translit_title' => 'O-kompanii123', 'text' => 'text', 'keyword' => '', 'description' => '', 'draft' => NULL, 'language' => '*');
	echo $db_query->update('article')->set($value)->get_sql();
}
catch(my_exception $exception){
	echo $exception->getMessage().'<br/>';
}
catch(Exception $exception){
	echo $exception->getMessage().'<br/>';
}
?>